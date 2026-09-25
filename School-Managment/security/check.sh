#!/usr/bin/env bash
# ============================================================================
#  ZiberEibar - comprobaciones de seguridad
#
#  Uso (Git Bash, desde la carpeta School-Managment):
#      php artisan serve --port=8001        # en otra terminal
#      BASE_URL=http://127.0.0.1:8001 bash security/check.sh
#
#  Variables opcionales:
#      BASE_URL          URL de la web en marcha (por defecto http://127.0.0.1:8000)
#      ADMIN_EMAIL / ADMIN_PASSWORD      cuenta de administrador de pruebas
#      STUDENT_EMAIL / STUDENT_PASSWORD  cuenta de alumno ACTIVADA de pruebas
#      SKIP_TESTS=1      no ejecutar la batería de tests automáticos (tarda ~1 min)
#
#  Las pruebas no borran ni modifican datos y no envían emails.
#  Genera un informe en security/reports/informe-FECHA.md
# ============================================================================

cd "$(dirname "$0")/.." || exit 1

BASE_URL="${BASE_URL:-http://127.0.0.1:8000}"
BASE_URL="${BASE_URL%/}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@educenter.es}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-password}"
STUDENT_EMAIL="${STUDENT_EMAIL:-maria@educenter.es}"
STUDENT_PASSWORD="${STUDENT_PASSWORD:-password}"

TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT
mkdir -p security/reports
REPORT="security/reports/informe-$(date +%Y%m%d-%H%M%S).md"

PASS=0; FAIL=0; WARN=0; SKIP=0
ROWS=""

G=$'\e[32m'; R=$'\e[31m'; Y=$'\e[33m'; B=$'\e[36m'; D=$'\e[2m'; N=$'\e[0m'

# record ESTADO ID CATEGORÍA PRUEBA DETALLE
record() {
    local status="$1" id="$2" cat="$3" title="$4" detail="${5:-}" color icon
    case "$status" in
        PASS) PASS=$((PASS + 1)); color="$G"; icon="✅" ;;
        FAIL) FAIL=$((FAIL + 1)); color="$R"; icon="❌" ;;
        WARN) WARN=$((WARN + 1)); color="$Y"; icon="⚠️" ;;
        SKIP) SKIP=$((SKIP + 1)); color="$D"; icon="⏭️" ;;
    esac
    printf '%s[%s]%s %-4s %s\n' "$color" "$status" "$N" "$id" "$title"
    [ -n "$detail" ] && printf '       %s%s%s\n' "$D" "$detail" "$N"
    ROWS+="| $id | $cat | $title | $icon $status | ${detail//|/\\|} |"$'\n'
}

section() { printf '\n%s== %s ==%s\n' "$B" "$1" "$N"; }

# ---------------------------------------------------------------- HTTP helpers
code_of() { curl -s -o /dev/null -w '%{http_code}' "$@"; }

new_jar() { : > "$TMP/$1.jar"; echo "$TMP/$1.jar"; }

# Loads a page with the jar and prints its CSRF token
token_for() {
    curl -s -c "$1" -b "$1" "$BASE_URL$2" -o "$TMP/token.html"
    grep -o 'name="_token" value="[^"]*"' "$TMP/token.html" | head -1 | sed 's/.*value="//; s/"$//'
}

# login JAR EMAIL PASSWORD -> prints where the login redirects to
login() {
    local token
    token="$(token_for "$1" /login)"
    curl -s -c "$1" -b "$1" -o /dev/null -w '%{redirect_url}' \
        --data-urlencode "_token=$token" --data-urlencode "email=$2" --data-urlencode "password=$3" \
        "$BASE_URL/login"
}

# post_follow JAR PATH TOKEN data... -> final page after redirects
post_follow() {
    local jar="$1" path="$2" token="$3"; shift 3
    local args=()
    for kv in "$@"; do args+=(--data-urlencode "$kv"); done
    curl -s -L -c "$jar" -b "$jar" --data-urlencode "_token=$token" "${args[@]}" "$BASE_URL$path"
}

random_email() { echo "no-existe-$RANDOM$RANDOM@ejemplo.test"; }

COMPOSER="$(command -v composer || command -v composer.bat)"

echo "ZiberEibar - comprobaciones de seguridad"
echo "Web: $BASE_URL"

# ============================================================== A. Dependencias
section "A. Dependencias con vulnerabilidades conocidas"

if [ -n "$COMPOSER" ]; then
    out="$("$COMPOSER" audit --no-interaction 2>&1)"; rc=$?
    if [ $rc -eq 0 ]; then
        record PASS A1 "A06 Componentes vulnerables" "composer audit (paquetes PHP)" "Sin avisos de seguridad"
    else
        record FAIL A1 "A06 Componentes vulnerables" "composer audit (paquetes PHP)" "$(echo "$out" | grep -iE 'advisor|cve|package' | head -3 | tr '\n' ' ')"
    fi
else
    record SKIP A1 "A06 Componentes vulnerables" "composer audit (paquetes PHP)" "composer no encontrado"
fi

if command -v npm >/dev/null 2>&1; then
    out="$(npm audit 2>&1)"; rc=$?
    if [ $rc -eq 0 ]; then
        record PASS A2 "A06 Componentes vulnerables" "npm audit (paquetes JavaScript)" "$(echo "$out" | tail -1)"
    else
        record FAIL A2 "A06 Componentes vulnerables" "npm audit (paquetes JavaScript)" "$(echo "$out" | grep -iE 'vulnerabilit' | tail -1)"
    fi
else
    record SKIP A2 "A06 Componentes vulnerables" "npm audit (paquetes JavaScript)" "npm no encontrado"
fi

# ======================================================= B. Configuración/secretos
section "B. Configuración y secretos"

if git ls-files --error-unmatch .env >/dev/null 2>&1; then
    record FAIL B1 "A05 Configuración" ".env fuera del repositorio" ".env está en git: sus claves son públicas"
else
    record PASS B1 "A05 Configuración" ".env fuera del repositorio" "git no lo sigue (.gitignore)"
fi

# Empty values and "null" (Laravel's defaults in .env.example) are not secrets
leaks="$(git log --all -p 2>/dev/null | grep -E '^\+(MAIL_PASSWORD|DB_PASSWORD|APP_KEY)=' | grep -vE '=(null|""|)[[:space:]]*$' | head -3)"
if [ -z "$leaks" ]; then
    record PASS B2 "A02 Fallos criptográficos" "Sin contraseñas ni claves en el historial de git" "Buscado MAIL_PASSWORD, APP_KEY y DB_PASSWORD en todos los commits"
else
    record FAIL B2 "A02 Fallos criptográficos" "Sin contraseñas ni claves en el historial de git" "Encontrado en el historial: cambia esas claves"
fi

if git ls-files | grep -qE '\.sqlite$'; then
    record FAIL B3 "A05 Configuración" "Base de datos fuera del repositorio" "Hay un .sqlite en git (datos personales)"
else
    record PASS B3 "A05 Configuración" "Base de datos fuera del repositorio" "Ningún .sqlite en git"
fi

app_debug="$(grep -E '^APP_DEBUG=' .env 2>/dev/null | cut -d= -f2)"
app_env="$(grep -E '^APP_ENV=' .env 2>/dev/null | cut -d= -f2)"
if [ "$app_debug" = "true" ]; then
    if [ "$app_env" = "production" ]; then
        record FAIL B4 "A05 Configuración" "APP_DEBUG desactivado" "APP_DEBUG=true en producción muestra código y configuración en los errores"
    else
        record WARN B4 "A05 Configuración" "APP_DEBUG desactivado" "APP_DEBUG=true (APP_ENV=$app_env): correcto en desarrollo, en producción debe ser false"
    fi
else
    record PASS B4 "A05 Configuración" "APP_DEBUG desactivado" "APP_DEBUG=$app_debug"
fi

driver="$(php artisan tinker --execute='echo config("hashing.driver");' 2>/dev/null | tail -1)"
if [ "$driver" = "argon2id" ]; then
    record PASS B5 "A02 Fallos criptográficos" "Contraseñas cifradas con argon2id" "hashing.driver = argon2id"
else
    record FAIL B5 "A02 Fallos criptográficos" "Contraseñas cifradas con argon2id" "hashing.driver = ${driver:-desconocido}"
fi

weak="$(php artisan tinker --execute='echo App\Models\User::where("password", "not like", "\$argon2id\$%")->count();' 2>/dev/null | tail -1)"
if [ "$weak" = "0" ]; then
    record PASS B6 "A02 Fallos criptográficos" "Ninguna contraseña guardada en claro o con un algoritmo antiguo" "Todos los hashes empiezan por \`\$argon2id\$\`"
else
    record WARN B6 "A02 Fallos criptográficos" "Ninguna contraseña guardada en claro o con un algoritmo antiguo" "$weak usuario(s) con otro formato: ejecuta php artisan migrate:fresh --seed"
fi

# ================================================================ C. Código
section "C. Revisión del código"

# welcome.blade.php es la plantilla por defecto de Laravel y no tiene ruta
raw="$(grep -rnE '\{!!' resources/views --include=*.blade.php | grep -v 'vendor/pagination\|welcome.blade' | head -3)"
if [ -z "$raw" ]; then
    record PASS C1 "A03 Inyección (XSS)" "Todas las vistas escapan la salida" "Ningún \`{!! !!}\` (salida sin escapar)"
else
    record FAIL C1 "A03 Inyección (XSS)" "Todas las vistas escapan la salida" "$raw"
fi

inline="$(grep -rnE ' on(click|change|submit|load|error|input|mouseover)=|<script>|javascript:' resources/views --include=*.blade.php | grep -v 'welcome.blade' | head -3)"
if [ -z "$inline" ]; then
    record PASS C2 "A03 Inyección (XSS)" "Sin JavaScript en línea (compatible con la CSP)" "Ningún \`onclick=\`, \`<script>\` en línea ni \`javascript:\`"
else
    record FAIL C2 "A03 Inyección (XSS)" "Sin JavaScript en línea (compatible con la CSP)" "$inline"
fi

if [ "${SKIP_TESTS:-0}" = "1" ]; then
    record SKIP C3 "Varias" "Batería de tests automáticos (php artisan test)" "Omitido con SKIP_TESTS=1"
else
    echo "       ejecutando php artisan test (tarda ~1 min)..."
    out="$(php artisan test 2>&1)"; rc=$?
    summary="$(echo "$out" | grep -oE '"tests":[0-9]+,"passed":[0-9]+|Tests: +[^\r]*' | head -1)"
    if [ $rc -eq 0 ]; then
        record PASS C3 "Varias" "Batería de tests automáticos (php artisan test)" "Todos pasan ${summary}"
    else
        record FAIL C3 "Varias" "Batería de tests automáticos (php artisan test)" "Hay tests fallando ${summary}"
    fi
fi

# ============================================================ D. Web en marcha
section "D. Pruebas contra la web en marcha ($BASE_URL)"

home="$(curl -s -D "$TMP/headers.txt" "$BASE_URL/" -o "$TMP/home.html" -w '%{http_code}')"
if [ "$home" != "200" ] || ! grep -q "ziber_eibar" "$TMP/home.html"; then
    record FAIL D0 "-" "La web responde" "No responde en $BASE_URL (código $home). Arranca php artisan serve y usa BASE_URL"
    DYNAMIC=0
else
    record PASS D0 "-" "La web responde" "HTTP 200 en $BASE_URL"
    DYNAMIC=1
fi

if [ "$DYNAMIC" = "1" ]; then
    H="$(tr -d '\r' < "$TMP/headers.txt")"

    check_header() { # id title regex detail
        if echo "$H" | grep -qiE "$3"; then
            record PASS "$1" "A05 Configuración" "$2" "$(echo "$H" | grep -iE "$3" | head -1 | cut -c1-120)"
        else
            record FAIL "$1" "A05 Configuración" "$2" "$4"
        fi
    }
    check_header D1 "Protección contra clickjacking" '^x-frame-options: *deny' "Falta X-Frame-Options: DENY"
    check_header D2 "Content-Security-Policy solo permite scripts propios" "^content-security-policy:.*script-src 'self'" "Falta la CSP o permite scripts externos"
    check_header D3 "El navegador no adivina tipos de archivo" '^x-content-type-options: *nosniff' "Falta X-Content-Type-Options: nosniff"
    check_header D4 "Referrer-Policy (no filtra URLs con tokens a otras webs)" '^referrer-policy:' "Falta Referrer-Policy"

    cookie="$(echo "$H" | grep -iE '^set-cookie: *[a-z0-9_-]*session=' | head -1)"
    if echo "$cookie" | grep -qi 'httponly' && echo "$cookie" | grep -qi 'samesite'; then
        record PASS D5 "A07 Autenticación" "Cookie de sesión con HttpOnly y SameSite" "JavaScript no puede leerla y no se envía desde otras webs"
    else
        record FAIL D5 "A07 Autenticación" "Cookie de sesión con HttpOnly y SameSite" "${cookie:-no se encontró cookie de sesión}"
    fi
    if echo "$BASE_URL" | grep -q '^https'; then
        if echo "$cookie" | grep -qi 'secure'; then
            record PASS D6 "A02 Fallos criptográficos" "Cookie de sesión solo por HTTPS (Secure)" "Secure activado"
        else
            record FAIL D6 "A02 Fallos criptográficos" "Cookie de sesión solo por HTTPS (Secure)" "Pon SESSION_SECURE_COOKIE=true"
        fi
    else
        record WARN D6 "A02 Fallos criptográficos" "Cookie de sesión solo por HTTPS (Secure)" "La web va por HTTP (normal en local). En producción: HTTPS + SESSION_SECURE_COOKIE=true"
    fi

    exposed=""
    for p in /.env /.env.example /.git/config /composer.json /composer.lock /artisan /phpunit.xml \
             /storage/logs/laravel.log /database/database.sqlite /vendor/autoload.php /../.env; do
        c="$(code_of --path-as-is "$BASE_URL$p")"
        [ "$c" = "200" ] && exposed+="$p "
    done
    if [ -z "$exposed" ]; then
        record PASS D7 "A05 Configuración" "Archivos sensibles no accesibles desde la web" ".env, .git, logs, base de datos, composer.json… devuelven 404"
    else
        record FAIL D7 "A05 Configuración" "Archivos sensibles no accesibles desde la web" "Accesibles: $exposed"
    fi

    c="$(code_of -X POST -d 'email=a@a.es&password=x' "$BASE_URL/login")"
    if [ "$c" = "419" ]; then
        record PASS D8 "A01 Control de acceso (CSRF)" "Formularios rechazados sin token CSRF" "POST /login sin token -> 419"
    else
        record FAIL D8 "A01 Control de acceso (CSRF)" "Formularios rechazados sin token CSRF" "POST /login sin token -> $c"
    fi

    loc="$(curl -s -o /dev/null -w '%{http_code} %{redirect_url}' "$BASE_URL/administracion")"
    if echo "$loc" | grep -qE '^302 .*/login$'; then
        record PASS D9 "A01 Control de acceso" "El panel de admin exige iniciar sesión" "GET /administracion sin sesión -> redirige a /login"
    else
        record FAIL D9 "A01 Control de acceso" "El panel de admin exige iniciar sesión" "GET /administracion sin sesión -> $loc"
    fi

    # Same error for an existing email with a wrong password and for an email that doesn't exist
    jar="$(new_jar enum)"; t="$(token_for "$jar" /login)"
    msg='Las credenciales no son correctas o la cuenta aún no está activada.'
    a="$(post_follow "$jar" /login "$t" "email=$ADMIN_EMAIL" "password=incorrecta-$RANDOM" | grep -c "$msg")"
    b="$(post_follow "$jar" /login "$t" "email=$(random_email)" "password=incorrecta" | grep -c "$msg")"
    if [ "$a" -ge 1 ] && [ "$b" -ge 1 ]; then
        record PASS D10 "A07 Autenticación" "El login no revela qué emails existen" "Mismo mensaje con email existente y con email inventado"
    else
        record FAIL D10 "A07 Autenticación" "El login no revela qué emails existen" "Los mensajes son distintos ($a/$b)"
    fi

    # Brute force: 5 wrong passwords, the 6th attempt is blocked
    jar="$(new_jar brute)"; t="$(token_for "$jar" /login)"; victim="$(random_email)"
    for i in 1 2 3 4 5; do post_follow "$jar" /login "$t" "email=$victim" "password=intento-$i" >/dev/null; done
    if post_follow "$jar" /login "$t" "email=$victim" "password=intento-6" | grep -q 'Demasiados intentos'; then
        record PASS D11 "A07 Autenticación" "Bloqueo por fuerza bruta en el login" "Tras 5 contraseñas incorrectas: «Demasiados intentos. Inténtalo de nuevo en X segundos.»"
    else
        record FAIL D11 "A07 Autenticación" "Bloqueo por fuerza bruta en el login" "El 6º intento no se bloquea"
    fi

    # Resend page: same answer for an activated student and for an unknown email (no email is sent)
    jar="$(new_jar resend)"; t="$(token_for "$jar" /register)"
    ok='Si existe una cuenta pendiente de activar con ese email'
    a="$(post_follow "$jar" /register "$t" "email=$STUDENT_EMAIL" | grep -c "$ok")"
    b="$(post_follow "$jar" /register "$t" "email=$(random_email)" | grep -c "$ok")"
    if [ "$a" -ge 1 ] && [ "$b" -ge 1 ]; then
        record PASS D12 "A07 Autenticación" "El reenvío de activación no revela qué emails existen" "Misma respuesta con email real y con email inventado"
    else
        record FAIL D12 "A07 Autenticación" "El reenvío de activación no revela qué emails existen" "Respuestas distintas ($a/$b)"
    fi

    jar="$(new_jar flood)"; t="$(token_for "$jar" /register)"; target="$(random_email)"; last=""
    for i in 1 2 3 4 5 6; do
        last="$(curl -s -o /dev/null -w '%{http_code}' -c "$jar" -b "$jar" --data-urlencode "_token=$t" --data-urlencode "email=$target" "$BASE_URL/register")"
    done
    if [ "$last" = "429" ]; then
        record PASS D13 "A04 Diseño inseguro" "Límite de peticiones en el reenvío de activación" "6ª petición seguida con el mismo email -> 429 Too Many Requests"
    else
        record FAIL D13 "A04 Diseño inseguro" "Límite de peticiones en el reenvío de activación" "6ª petición -> $last"
    fi

    c="$(code_of "$BASE_URL/activar/token-falso-123?email=$(random_email)")"
    if [ "$c" = "302" ]; then
        record PASS D14 "A07 Autenticación" "Un enlace de activación falso no sirve" "GET /activar/token-falso -> redirige a /register sin mostrar datos"
    else
        record FAIL D14 "A07 Autenticación" "Un enlace de activación falso no sirve" "GET /activar/token-falso -> $c"
    fi

    inactive="$(php artisan tinker --execute='echo App\Models\Course::where("status", "inactive")->value("id");' 2>/dev/null | tail -1)"
    if [ -n "$inactive" ]; then
        c="$(code_of "$BASE_URL/cursos/$inactive")"
        if [ "$c" = "404" ]; then
            record PASS D15 "A01 Control de acceso" "Los cursos inactivos no son públicos" "GET /cursos/$inactive (inactivo) -> 404"
        else
            record FAIL D15 "A01 Control de acceso" "Los cursos inactivos no son públicos" "GET /cursos/$inactive (inactivo) -> $c"
        fi
    else
        record SKIP D15 "A01 Control de acceso" "Los cursos inactivos no son públicos" "No hay ningún curso inactivo para probar"
    fi

    body="$(curl -s "$BASE_URL/cursos/99999999" -w '\n%{http_code}')"
    if [ "$(echo "$body" | tail -1)" = "404" ] && ! echo "$body" | grep -qiE 'stack trace|vendor/laravel|SQLSTATE'; then
        record PASS D16 "A05 Configuración" "Las páginas de error no muestran detalles internos" "404 propio, sin traza ni rutas del servidor"
    else
        record FAIL D16 "A05 Configuración" "Las páginas de error no muestran detalles internos" "La página de error muestra información interna"
    fi

    # ---------------------------------------------------------- As a student
    jar="$(new_jar student)"
    where="$(login "$jar" "$STUDENT_EMAIL" "$STUDENT_PASSWORD")"
    if echo "$where" | grep -q '/cursos'; then
        c1="$(code_of -b "$jar" "$BASE_URL/administracion")"
        c2="$(code_of -b "$jar" "$BASE_URL/administracion/alumnos/1")"
        t="$(token_for "$jar" /mis-matriculas)"
        c3="$(code_of -b "$jar" -c "$jar" --data-urlencode "_token=$t" --data-urlencode "name=Hack" "$BASE_URL/administracion/alumnos")"
        if [ "$c1" = "403" ] && [ "$c2" = "403" ] && [ "$c3" = "403" ]; then
            record PASS D17 "A01 Control de acceso" "Un alumno no puede usar el panel de admin" "Ver panel, ver otro alumno y crear alumno -> 403"
        else
            record FAIL D17 "A01 Control de acceso" "Un alumno no puede usar el panel de admin" "Respuestas: $c1 / $c2 / $c3"
        fi
    else
        record SKIP D17 "A01 Control de acceso" "Un alumno no puede usar el panel de admin" "No se pudo iniciar sesión como $STUDENT_EMAIL (usa STUDENT_EMAIL/STUDENT_PASSWORD)"
    fi

    # ------------------------------------------------------------ As an admin
    jar="$(new_jar admin)"
    where="$(login "$jar" "$ADMIN_EMAIL" "$ADMIN_PASSWORD")"
    if echo "$where" | grep -q '/administracion'; then
        sqli_ok=1; sqli_detail=""
        for payload in "' OR '1'='1" "%' OR 1=1 --" "' UNION SELECT password, email FROM users --" "1; DROP TABLE users; --"; do
            page="$(curl -s -G -b "$jar" --data-urlencode "search=$payload" "$BASE_URL/administracion/alumnos")"
            if echo "$page" | grep -qiE 'SQLSTATE|syntax error|\$argon2id\$' || ! echo "$page" | grep -q '>0 alumnos con estos filtros'; then
                sqli_ok=0; sqli_detail+="[$payload] "
            fi
        done
        if [ $sqli_ok = 1 ]; then
            record PASS D18 "A03 Inyección (SQL)" "El buscador no es vulnerable a inyección SQL" "4 cargas (OR 1=1, UNION, DROP…) -> 0 resultados, sin errores SQL"
        else
            record FAIL D18 "A03 Inyección (SQL)" "El buscador no es vulnerable a inyección SQL" "Respuesta sospechosa con: $sqli_detail"
        fi

        xss='<script>alert(1)</script>'
        page="$(curl -s -G -b "$jar" --data-urlencode "search=$xss" "$BASE_URL/administracion/alumnos")"
        if echo "$page" | grep -qF '&lt;script&gt;alert(1)&lt;/script&gt;' && ! echo "$page" | grep -qF "$xss"; then
            record PASS D19 "A03 Inyección (XSS)" "El buscador no es vulnerable a XSS reflejado" "\`<script>\` se devuelve escapado como \`&lt;script&gt;\`"
        else
            record FAIL D19 "A03 Inyección (XSS)" "El buscador no es vulnerable a XSS reflejado" "El \`<script>\` aparece sin escapar"
        fi

        c="$(code_of -g -b "$jar" "$BASE_URL/administracion/alumnos?search[]=x")"
        if [ "$c" = "302" ]; then
            record PASS D20 "A04 Diseño inseguro" "Parámetros manipulados no provocan errores 500" "search[]=x (array) -> se rechaza con validación (302)"
        else
            record FAIL D20 "A04 Diseño inseguro" "Parámetros manipulados no provocan errores 500" "search[]=x -> $c"
        fi

        # Logout must invalidate the session: an old copy of the cookie no longer works
        cp "$jar" "$TMP/stolen.jar"
        t="$(token_for "$jar" /administracion)"
        curl -s -o /dev/null -b "$jar" -c "$jar" --data-urlencode "_token=$t" "$BASE_URL/logout"
        c="$(code_of -b "$TMP/stolen.jar" "$BASE_URL/administracion")"
        if [ "$c" = "302" ]; then
            record PASS D21 "A07 Autenticación" "Cerrar sesión invalida la sesión en el servidor" "Una copia de la cookie anterior ya no da acceso (302 a /login)"
        else
            record FAIL D21 "A07 Autenticación" "Cerrar sesión invalida la sesión en el servidor" "La cookie antigua sigue funcionando ($c)"
        fi
    else
        for id in D18 D19 D20 D21; do
            record SKIP "$id" "-" "Pruebas como administrador" "No se pudo iniciar sesión como $ADMIN_EMAIL (usa ADMIN_EMAIL/ADMIN_PASSWORD)"
        done
    fi
fi

# ================================================================ Informe
TOTAL=$((PASS + FAIL + WARN + SKIP))
{
    echo "# Informe de seguridad - ZiberEibar"
    echo
    echo "- **Fecha:** $(date '+%d/%m/%Y %H:%M')"
    echo "- **Web probada:** $BASE_URL"
    echo "- **Commit:** $(git rev-parse --short HEAD 2>/dev/null) ($(git rev-parse --abbrev-ref HEAD 2>/dev/null))"
    echo "- **PHP:** $(php -r 'echo PHP_VERSION;') · **Laravel:** $(php artisan --version 2>/dev/null | grep -oE '[0-9]+\.[0-9]+\.[0-9]+')"
    echo
    echo "**Resultado:** $PASS superadas · $FAIL fallidas · $WARN avisos · $SKIP omitidas (de $TOTAL)"
    echo
    echo "| ID | Categoría (OWASP Top 10) | Prueba | Resultado | Detalle |"
    echo "|---|---|---|---|---|"
    printf '%s' "$ROWS"
} > "$REPORT"

printf '\n%sResultado:%s %s%d superadas%s · %s%d fallidas%s · %s%d avisos%s · %d omitidas\n' \
    "$B" "$N" "$G" "$PASS" "$N" "$R" "$FAIL" "$N" "$Y" "$WARN" "$N" "$SKIP"
echo "Informe guardado en $REPORT"

[ "$FAIL" -eq 0 ]
