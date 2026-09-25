# Pruebas de seguridad - ZiberEibar

Esta carpeta contiene el script con el que comprobamos la seguridad de la aplicación y los informes que genera.

| Archivo | Qué es |
|---|---|
| `check.sh` | Script de pruebas (33 comprobaciones). No borra ni modifica datos y no envía emails. |
| `reports/informe-FECHA.md` | Informe que genera cada ejecución, con el resultado de cada prueba. |

## 1. Cómo ejecutarlo

Requisitos: **Git Bash** (viene con Git para Windows), PHP, Composer y Node, los mismos que para el proyecto.

```bash
# Terminal 1: arrancar la web
cd School-Managment
php artisan serve --port=8001

# Terminal 2: lanzar las pruebas
cd School-Managment
BASE_URL=http://127.0.0.1:8001 bash security/check.sh
```

Variables opcionales:

| Variable | Por defecto | Para qué |
|---|---|---|
| `BASE_URL` | `http://127.0.0.1:8000` | Dirección de la web que se prueba |
| `ADMIN_EMAIL` / `ADMIN_PASSWORD` | `admin@educenter.es` / `password` | Cuenta de administrador de pruebas (la del seeder) |
| `STUDENT_EMAIL` / `STUDENT_PASSWORD` | `maria@educenter.es` / `password` | Cuenta de alumno **ya activada** |
| `SKIP_TESTS=1` | - | No ejecutar los tests automáticos (ahorra ~1 minuto) |

Cada resultado es:
- **PASS**: la protección funciona.
- **FAIL**: hay un problema.
- **WARN**: correcto en local, pero hay que cambiarlo antes de publicar la web.
- **SKIP**: no se pudo probar (por ejemplo, falta una cuenta).

> Algunas pruebas provocan a propósito los bloqueos por exceso de intentos. Si ejecutas el script dos veces seguidas, espera 1 minuto entre ejecuciones.

## 2. Qué comprueba cada prueba

Las categorías siguen el **OWASP Top 10 (2021)**, la lista de referencia de riesgos de aplicaciones web.

### A. Dependencias

| ID | Qué se comprueba | Cómo | Resultado esperado |
|---|---|---|---|
| A1 | Paquetes PHP con vulnerabilidades conocidas (CVE) | `composer audit` | Sin avisos |
| A2 | Paquetes JavaScript con vulnerabilidades conocidas | `npm audit` | `found 0 vulnerabilities` |

### B. Configuración y secretos

| ID | Qué se comprueba | Cómo | Resultado esperado |
|---|---|---|---|
| B1 | El `.env` (claves, contraseña de Gmail) no está en git | `git ls-files --error-unmatch .env` | Git no lo sigue |
| B2 | No hay contraseñas ni claves en ningún commit del historial | `git log --all -p` buscando `MAIL_PASSWORD`, `APP_KEY`, `DB_PASSWORD` | Solo valores vacíos o `null` |
| B3 | La base de datos (con datos personales) no está en git | `git ls-files` buscando `.sqlite` | Ninguna |
| B4 | Modo depuración | `APP_DEBUG` en `.env` | `false` en producción (en local sale WARN) |
| B5 | Algoritmo de las contraseñas | `config('hashing.driver')` | `argon2id` |
| B6 | Ninguna contraseña guardada en claro o con un algoritmo antiguo | Consulta a la base de datos | Todos los hashes empiezan por `$argon2id$` |

### C. Código

| ID | Qué se comprueba | Cómo | Resultado esperado |
|---|---|---|---|
| C1 | Ninguna vista imprime datos sin escapar (XSS) | Buscar `{!! !!}` en las vistas | Ninguno |
| C2 | Sin JavaScript en línea, que la CSP bloquearía | Buscar `onclick=`, `<script>` en línea, `javascript:` | Ninguno |
| C3 | Tests automáticos (incluyen los de seguridad) | `php artisan test` | Todos pasan |

### D. Ataques contra la web en marcha

| ID | Ataque que simula | Cómo se prueba | Resultado esperado | Dónde está la protección |
|---|---|---|---|---|
| D1 | Clickjacking (cargar la web en un iframe invisible) | Cabecera `X-Frame-Options` | `DENY` | `app/Http/Middleware/SecurityHeaders.php` |
| D2 | Ejecutar scripts inyectados | Cabecera `Content-Security-Policy` | `script-src 'self'` | `SecurityHeaders.php` |
| D3 | Que el navegador interprete un archivo como otro tipo | `X-Content-Type-Options` | `nosniff` | `SecurityHeaders.php` |
| D4 | Filtrar URLs con tokens a otras webs | `Referrer-Policy` | `strict-origin-when-cross-origin` | `SecurityHeaders.php` |
| D5 | Robar la cookie de sesión con JavaScript o usarla desde otra web | Atributos de la cookie | `HttpOnly` y `SameSite` | `config/session.php` |
| D6 | Robar la sesión en una red insegura | Atributo `Secure` | Solo con HTTPS (en local sale WARN) | `.env`: `SESSION_SECURE_COOKIE` |
| D7 | Descargar archivos internos (`/.env`, `/.git/config`, logs, base de datos) | Pedir cada ruta | 404 | La web solo publica `public/` |
| D8 | CSRF (enviar un formulario desde otra web) | `POST /login` sin token | 419 | Middleware CSRF de Laravel |
| D9 | Entrar al panel sin iniciar sesión | `GET /administracion` | Redirige a `/login` | Middleware `auth` + `admin` |
| D10 | Averiguar qué emails tienen cuenta (enumeración) | Login con email real y con email inventado | Mismo mensaje | `AuthController::login` |
| D11 | Fuerza bruta de contraseñas | 6 contraseñas incorrectas seguidas | «Demasiados intentos…» | `AuthController::login` (RateLimiter) |
| D12 | Enumeración en el reenvío de activación | Email real y email inventado | Misma respuesta | `AuthController::register` |
| D13 | Inundar el reenvío de activación | 6 peticiones seguidas | 429 Too Many Requests | `AppServiceProvider` (`throttle:register`) |
| D14 | Activar una cuenta con un enlace falso | `GET /activar/token-falso` | Redirige sin mostrar datos | `AccountActivation::isValid` |
| D15 | Ver cursos ocultos cambiando el ID en la URL | `GET /cursos/{id inactivo}` | 404 | `CourseController::show` |
| D16 | Obtener información interna provocando errores | `GET /cursos/99999999` | 404 propio, sin traza | `resources/views/errors/` |
| D17 | Escalada de privilegios (un alumno usa el panel de admin) | Como alumno: ver panel, ver alumno, crear alumno | 403 | Middleware `admin` |
| D18 | Inyección SQL en el buscador | `' OR '1'='1`, `UNION SELECT`, `DROP TABLE` | 0 resultados, sin errores SQL | Eloquent usa consultas preparadas |
| D19 | XSS reflejado | `search=<script>alert(1)</script>` | Se devuelve como `&lt;script&gt;` | Blade escapa con `{{ }}` |
| D20 | Manipular parámetros para provocar errores 500 | `search[]=x` | Validación (302) | Validación en los controladores de admin |
| D21 | Reutilizar una sesión después de cerrarla | Copia de la cookie tras cerrar sesión | Ya no da acceso | `AuthController::logout` |

## 3. Comandos manuales (para capturas del documento)

Las mismas pruebas, una a una, en Git Bash. Sustituye `8001` por tu puerto.

```bash
# Cabeceras de seguridad (D1-D4)
curl -sI http://127.0.0.1:8001/ | grep -iE "x-frame|content-security|x-content-type|referrer|set-cookie"

# Archivos sensibles (D7): deben dar 404
for f in /.env /.git/config /storage/logs/laravel.log /database/database.sqlite /composer.json; do
  echo "$f -> $(curl -s -o /dev/null -w '%{http_code}' --path-as-is http://127.0.0.1:8001$f)"
done

# CSRF (D8): sin token debe dar 419
curl -s -o /dev/null -w '%{http_code}\n' -X POST -d 'email=a@a.es&password=x' http://127.0.0.1:8001/login

# Panel sin sesión (D9): debe redirigir a /login
curl -s -o /dev/null -w '%{http_code} -> %{redirect_url}\n' http://127.0.0.1:8001/administracion

# Dependencias (A1, A2)
composer audit
npm audit
```

## 4. Herramientas externas (opcional)

Si queréis completar el documento con escáneres profesionales, estas herramientas se pueden lanzar contra vuestra propia web en local. **No las uséis nunca contra webs que no sean vuestras.**

```bash
# OWASP ZAP (escaneo pasivo básico), necesita Docker
docker run --rm -t ghcr.io/zaproxy/zaproxy:stable zap-baseline.py -t http://host.docker.internal:8001

# Nikto (configuración del servidor web)
nikto -h http://127.0.0.1:8001

# sqlmap sobre el buscador de alumnos (hace falta la cookie de una sesión de admin,
# cópiala desde las herramientas de desarrollador del navegador)
sqlmap -u "http://127.0.0.1:8001/administracion/alumnos?search=a" \
       --cookie="educenter-session=VALOR_DE_LA_COOKIE" --batch --level=2 --risk=1
```

## 5. Vulnerabilidades encontradas y corregidas

Durante el proyecto hicimos dos auditorías del código. Esto es lo que encontramos y cómo quedó:

| # | Problema encontrado | Gravedad | Solución | Prueba que lo demuestra |
|---|---|---|---|---|
| 1 | El login no limitaba los intentos: se podían probar contraseñas sin fin | Alta | Bloqueo de 1 minuto tras 5 fallos por email + IP | D11, `LoginTest` |
| 2 | Requisito: contraseñas con argon2id (Laravel usa bcrypt por defecto) | - (requisito) | Cambio a argon2id por defecto en `config/hashing.php` | B5, B6 |
| 3 | La cuenta se activaba solo con email + DNI: quien conociera el DNI de un compañero podía quedarse con su cuenta | Media | Enlace de activación de un solo uso enviado por email (caduca en 7 días, guardado cifrado) | D14, `ActivationTest` |
| 4 | Sin cabeceras de seguridad: posible clickjacking y sin CSP | Media | Middleware `SecurityHeaders` | D1-D4, `SecurityTest` |
| 5 | El límite de `/register` era por IP: en clase (misma IP) bloqueaba a todos; el login no tenía límite por IP | Media | Límites por email + IP y límite por IP más amplio | D13, `SecurityTest` |
| 6 | `?search[]=x` provocaba un error 500 que en modo depuración mostraba código | Baja | Validación de los filtros | D20 |
| 7 | Los cursos inactivos se podían ver cambiando el número de la URL | Baja | 404 salvo para admin o alumnos matriculados | D15 |
| 8 | El seeder crea `admin@educenter.es` / `password` y se podía ejecutar en producción | Alta si se publica | El seeder se niega a ejecutarse en producción | `SecurityTest` |
| 9 | El reenvío público de activación no tenía límite diario: se podía bombardear a un alumno con emails y agotar el límite diario de Gmail | Media | Máximo 5 emails al día por alumno desde la web pública | `ActivationTest` |

## 6. Riesgos conocidos que no se han corregido

| Riesgo | Por qué sigue abierto | Recomendación |
|---|---|---|
| Fuerza bruta distribuida (muchas IPs contra una cuenta) | Los límites son por IP; un límite global por cuenta permitiría bloquear la cuenta de otro a propósito | Añadir verificación en dos pasos (2FA) para los administradores |
| El reenvío de activación tarda más si el email es de un alumno pendiente (se envía un correo) | Diferencia de tiempo pequeña, solo revela que la cuenta está pendiente | Enviar los emails en una cola (`ShouldQueue` + `php artisan queue:work`) |
| `APP_DEBUG=true` y HTTP sin cifrar | Es lo normal en local | En producción: `APP_ENV=production`, `APP_DEBUG=false`, HTTPS y `SESSION_SECURE_COOKIE=true` |
| Cuentas de prueba con contraseña `password` | Son datos del seeder para desarrollo | No ejecutar el seeder en el servidor real; crear el admin con una contraseña fuerte |
| No hay registro de auditoría (quién borró o cambió qué) | Fuera del alcance del proyecto (OWASP A09) | Registrar las acciones del panel de admin |
| La contraseña de aplicación de Gmail se compartió por chat durante el desarrollo | - | Revocarla en myaccount.google.com/apppasswords y crear una nueva |
