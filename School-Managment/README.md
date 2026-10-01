# EduCenter · ZiberEibar

**EduCenter** ZiberEibar-eko ikastaroak, ikasleak eta matrikulak kudeatzeko web-aplikazioa da. Laravel-ekin garatu da, eta segurtasuna izan da diseinuaren oinarria: pasahitzak argon2id-rekin gordetzen dira, kontuak email bidez aktibatzen dira, saiakerak mugatuta daude eta Docker bidezko inplementazioa gotortuta dago.

- [Ezaugarriak](#ezaugarriak)
- [Teknologiak](#teknologiak)
- [Arkitektura](#arkitektura)
- [Instalazioa lokalean](#instalazioa-lokalean)
- [Inplementazioa Docker-ekin](#inplementazioa-docker-ekin)
- [Segurtasuna](#segurtasuna)
- [Testak](#testak)
- [Hizkuntzak](#hizkuntzak)
- [Proiektuaren egitura](#proiektuaren-egitura)

## Ezaugarriak

### Ikasleak

- Ikastaro aktiboen katalogoa ikusi: plaza libreak, datak, irakasgaiak eta irakasleak.
- Ikastaro batean matrikulatu, plazarik geratzen bada. Bi ikaslek ezin dute azken plaza aldi berean hartu.
- Beren matrikulak eta profila ikusi.

### Administratzaileak

- **Panela**: ikasle, ikastaro, matrikula eta plaza libreen estatistikak.
- **Ikasleak**: sortu, editatu, ezabatu eta bilatu. Administratzaileak ikasle bat sortzen duenean, ikasleak aktibazio-esteka bat jasotzen du emailez eta berak aukeratzen du pasahitza.
- **Irakasleak**: irakasle berria sortu ("irakasle berria" lasterbidea) eta ikastaro oso bati, ikastaroaren irakasgai batzuei edo bertan sortutako irakasgai berri bati esleitu. Ikasleek bezala, aktibazio-esteka bat jasotzen du emailez. Ikastaroaren fitxan haren irakasleak ikusten dira.
- **Aktibazioak**: kontua oraindik aktibatu ez duten ikasleen zerrenda. Esteka berriro bidal daiteke, banaka edo guztiei batera.
- **Ikastaroak**: sortu, editatu eta ezabatu (edukiera, datak, ikasturtea eta egoera).
- **Matrikulak**: ikasle bat ikastaro batean matrikulatu, eta matrikula bat bertan behera utzi edo berriro aktibatu.

### Irakasleak

Administratzaileak sortzen ditu, eta kontua aktibatzeko esteka jasotzen dute emailez. Saioa hasi eta ikastaroen katalogoa ikus dezakete. Ikastaro bakoitzaren irakasgaietan irakasle gisa agertzen dira. Oraingoz ez dute panel propiorik.

### Guztientzat

- Hiru hizkuntza: euskara, gaztelania eta ingelesa.
- Ezarpenak: gai iluna edo argia, testuaren tamaina eta hizkuntza. Saioa hasita, emaila eta pasahitza ere alda daitezke.
- Errore-orri propioak (403, 404, 419, 429, 500 eta 503).
- Pribatutasun-politika eta erabilera-baldintzak hiru hizkuntzetan.

### Kontu baten aktibazioa

1. Administratzaileak ikaslea (edo irakaslea) sortzen du.
2. Ikasleak aktibazio-esteka bat jasotzen du emailez. Esteka behin bakarrik erabil daiteke, 7 egunean iraungitzen da eta datu-basean hash eginda gordetzen da.
3. Ikasleak bere pasahitza aukeratzen du (gutxienez 8 karaktere, letrak eta zenbakiak) eta kontua aktibatuta geratzen da. Ordura arte ezin da saioa hasi.
4. Esteka iraungi bada, ikasleak berri bat eska dezake `/register` orrian (egunean 5 gehienez), edo administratzaileak bidal diezaioke.

### Bide nagusiak

| URL | Nork | Zer |
|---|---|---|
| `/` | Guztiek | Hasiera-orria |
| `/cursos` | Guztiek | Ikastaroen katalogoa |
| `/login` | Saioa hasi gabe | Saioa hasi |
| `/register` | Saioa hasi gabe | Aktibazio-esteka berriro eskatu |
| `/activar/{token}` | Saioa hasi gabe | Kontua aktibatu eta pasahitza aukeratu |
| `/mis-matriculas`, `/mi-perfil` | Ikasleak | Matrikulak eta profila |
| `/ajustes` | Guztiek | Ezarpenak |
| `/administracion` | Administratzaileak | Administrazio-panela |
| `/administracion/profesores/crear` | Administratzaileak | Irakasle berria sortu eta ikastaro edo irakasgai bati esleitu |

## Teknologiak

| | |
|---|---|
| Backend-a | Laravel 13, PHP 8.3 edo berriagoa (Docker irudian PHP 8.5) |
| Datu-basea | SQLite |
| Interfazea | Blade txantiloiak, CSS eta JavaScript propioak (`public/css`, `public/js`) |
| Pasahitzak | argon2id |
| Emailak | SMTP (Gmail) |
| Inplementazioa | Docker Compose: nginx + PHP-FPM |
| Testak | PHPUnit |

## Arkitektura

Aplikazioak **MVC** ereduari jarraitzen dio, eta negozio-logika konplexuena zerbitzu-geruza batean dago:

| Geruza | Karpeta | Zer egiten du |
|---|---|---|
| Ereduak (*Model*) | `app/Models` | Datuak, taulen arteko erlazioak eta arau txikiak (adib. `Course::hasAvailableSpots()`) |
| Bistak (*View*) | `resources/views` | Blade txantiloiak, ataleka: `admin/`, `student/`, `auth/`, `home/`, `settings/` |
| Kontrolatzaileak (*Controller*) | `app/Http/Controllers` | Eskaera jaso, datuak lortu eta bista edo birbideraketa bat itzuli. Administrazio-panelekoak `Admin/` azpikarpetan daude |
| Zerbitzuak | `app/Services` | `EnrollmentService`: matrikulak, transakzio eta blokeo batekin. `AccountActivation`: aktibazio-estekak |
| Middleware-ak | `app/Http/Middleware` | Rolen egiaztapena (`admin`, `student`), hizkuntza eta segurtasun-goiburuak |
| Jakinarazpenak | `app/Notifications` | Aktibazio-emaila eta kontu-aldaketen abisua |
| Bideak | `routes/web.php` | URL bakoitza zein kontrolatzailera doan |

Datu-eredua:

- `users` eta `roles`: erabiltzaileak eta haien rola (`admin`, `teacher` edo `student`).
- `courses`: ikastaroak.
- `subjects` eta `course_subject`: ikastaro bakoitzaren irakasgaiak eta irakaslea.
- `course_teacher`: ikastaro osoari esleitutako irakasleak.
- `enrollments`: matrikulak (ikaslea + ikastaroa, `active` edo `cancelled` egoeran).
- `grades`: kalifikazioak. Datu-basean daude, baina oraingoz ez dute interfazerik.

## Instalazioa lokalean

Behar dena: **PHP 8.3** edo berriagoa (`pdo_sqlite` luzapenarekin) eta **Composer**. Node.js ez da behar aplikazioa exekutatzeko, segurtasun-probetako `npm audit` egiteko bakarrik.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed    # SQLite fitxategia sortzeko galdetzen badu, erantzun "yes"
php artisan serve
```

Ondoren, ireki <http://localhost:8000>.

### Proba-kontuak

`--seed` aukerak proba-datuak sortzen ditu: ikastaroak, irakasgaiak, matrikulak eta kontu hauek.

| Rola | Emaila | Pasahitza |
|---|---|---|
| Administratzailea | `admin@educenter.es` | `password` |
| Ikaslea (aktibatua) | `maria@educenter.es`, `carlos@educenter.es` | `password` |
| Ikaslea (aktibatu gabe) | `ana@educenter.es`, `inaki@educenter.es` | ezin dute saioa hasi |
| Irakaslea | `jon@educenter.es`, `laura@educenter.es` | `password` |

> [!WARNING]
> Kontu hauen pasahitzak publikoak dira. Horregatik, seeder-ak ez du funtzionatzen `APP_ENV=production` denean.

### Emailak lokalean

`.env.example` fitxategian `MAIL_MAILER=log` dago. Horrela, emailak ez dira bidaltzen, `storage/logs/laravel.log` fitxategian idazten dira, eta aktibazio-estekak han aurkituko dituzu. Benetan bidaltzeko, sortu Gmail-eko aplikazio-pasahitz bat eta bete `.env` fitxategiko `MAIL_*` aldagaiak. `.env.example` fitxategian pausoz pauso azalduta dago.

## Inplementazioa Docker-ekin

Produkziorako hiru edukiontzi daude:

- **app**: PHP-FPM eta aplikazioa, `www-data` erabiltzailearekin.
- **queue**: `app`-en irudi bera. Emailak (aktibazio-estekak eta kontu-abisuak) web-eskaeratik kanpo bidaltzen ditu, eta horrela posta-zerbitzari motel edo erori batek ez ditu orriak blokeatzen. Bidalketak huts egiten badu, 3 aldiz saiatzen da, minutu bateko tartearekin. Aktibazio-email bat ezin bada bidali, haren esteka ezabatu egiten da, eta administratzaileak berriro bidal dezake.
- **web**: nginx. `public/` karpeta bakarrik zerbitzatzen du HTTPS bidez, eta PHP eskaerak `app` edukiontzira bidaltzen ditu.

Datu-basea eta `storage/` karpeta Docker bolumenetan gordetzen dira (`educenter_database` eta `educenter_storage`), beraz ez dira galtzen edukiontziak berreraikitzean.

### Abiaraztea

Docker eta Docker Compose v2 instalatuta dituen zerbitzari batean:

```bash
cp .env.docker.example .env
# Bete .env fitxategian: SITE_HOST, APP_URL, APP_KEY eta MAIL_PASSWORD
echo "base64:$(openssl rand -base64 32)"   # APP_KEY sortzeko
docker compose up -d --build
```

Webgunea `https://SITE_HOST` helbidean egongo da (`http://` helbidea `https://`-ra birbideratzen da).

`app` edukiontzia abiarazten den bakoitzean, automatikoki:

1. `APP_KEY` hutsik badago, ez da abiarazten.
2. SQLite datu-basea sortzen du, ez badago.
3. Migrazioak exekutatzen ditu.
4. Konfigurazioa, bideak eta bistak cachean gordetzen ditu (`php artisan optimize`).

### HTTPS ziurtagiria

`docker/certs/` karpetan `server.crt` eta `server.key` ez badaude, `SITE_HOST`-erako ziurtagiri auto-sinatu bat sortzen da. Benetako ziurtagiri bat erabiltzeko, jarri bi fitxategi horiek karpeta horretan eta berrabiarazi `web` edukiontzia:

```bash
docker compose restart web
```

### Lehen administratzailea

Produkzioan seeder-a ez da exekutatzen, beraz lehen administratzailea eskuz sortu behar da:

```bash
docker compose exec app php artisan tinker
```

```php
App\Models\User::create([
    'name' => 'Admin',
    'surname' => 'ZiberEibar',
    'email' => 'admin@adibidea.eus',
    'password' => 'pasahitz-luze-eta-sendo-bat',
    'role_id' => App\Models\Role::where('name', 'admin')->value('id'),
    'is_registered' => true,
]);
```

Pasahitza automatikoki gordetzen da argon2id-rekin. Tinker erabiltzen da pasahitza shell-aren historian gera ez dadin.

### Eguneratzea

Kodea eguneratu ondoren (adib. `git pull`):

```bash
docker compose up -d --build
```

Migrazio berriak automatikoki exekutatzen dira edukiontzia abiaraztean.

### Edukiontzien segurtasuna

- Kodea `root` erabiltzailearena da eta PHP-k ezin du aldatu. PHP-k `storage/`, `bootstrap/cache/` eta datu-basea bakarrik idatz ditzake.
- `web` edukiontzian `public/` karpeta bakarrik dago: `.env`, datu-basea, logak eta kodea ez dira hara iristen.
- Edukiontziek Linux gaitasun guztiak galtzen dituzte (`cap_drop: ALL`), eta nginx-ek behar dituenak bakarrik berreskuratzen ditu. `no-new-privileges` aktibatuta dago.
- Memoria- eta prozesu-mugak daude, prozesu batek zerbitzari osoa bota ez dezan.
- nginx-ek `SITE_HOST` izenari bakarrik erantzuten dio, `Host` goiburua aldatuz aktibazio-esteka faltsuak sor ez daitezen.
- Saio-cookieak `Secure` dira eta zifratuta gordetzen dira.
- Logak errotatu egiten dira (10 MB × 5 fitxategi).

## Segurtasuna

Aplikazioaren babes nagusiak:

- Pasahitzak argon2id-rekin gordetzen dira.
- Saio-hasiera: 5 huts egite email + IP bakoitzeko, eta ondoren minutu bateko blokeoa. Gainera, IP bakoitzeko 60 saiakera minutuko (*password spraying*-aren aurka).
- Erantzuna berdina da, email bat existitu ala ez, erasotzaileak ez dezan jakin zein kontu dauden.
- Segurtasun-goiburuak: CSP (`script-src 'self'`), `X-Frame-Options: DENY`, `nosniff`, `Referrer-Policy` eta HSTS (HTTPS erabiltzean).
- CSRF babesa, Blade-ren ihes automatikoa (XSS) eta Eloquent-en kontsulta prestatuak (SQL injekzioa).
- Rolen araberako sarbidea `admin` eta `student` middleware-en bidez.

`security/check.sh` script-ak 33 egiaztapen egiten ditu, OWASP Top 10 oinarri hartuta. Windows-en, Git Bash-en exekutatu:

```bash
php artisan serve --port=8001                               # 1. terminala
BASE_URL=http://127.0.0.1:8001 bash security/check.sh       # 2. terminala
```

Proba bakoitzaren azalpena, txostenak eta aurkitutako ahultasunak: [security/README.md](security/README.md).

## Testak

```bash
php artisan test
```

`tests/Feature` karpetan 150 test inguru daude: saio-hasiera, aktibazioa, matrikulak, administrazio-panela, hizkuntzak eta segurtasuna.

## Hizkuntzak

Itzulpenak `lang/` karpetan daude: `eu.json`, `es.json` eta `en.json`, eta balidazio-mezuak `lang/eu/` eta `lang/es/` karpetetan.

Kodean testuak gaztelaniaz idazten dira `__('...')` barruan, eta testu horiek dira JSON fitxategietako gakoak. Testu berri bat gehitzean, gehitu haren itzulpena hiru fitxategietan.

Hizkuntza lehenetsia `.env` fitxategiko `APP_LOCALE` da (`es`). Erabiltzaile bakoitzak `/ajustes` orrian alda dezake.

## Proiektuaren egitura

```
School-Managment/
├── app/
│   ├── Http/Controllers/    Kontrolatzaileak (Admin/ = administrazio-panela)
│   ├── Http/Middleware/     Rolak, hizkuntza eta segurtasun-goiburuak
│   ├── Models/              Eloquent ereduak
│   ├── Notifications/       Emailak
│   └── Services/            Negozio-logika (matrikulak, aktibazioa)
├── config/                  Laravel-en konfigurazioa
├── database/                Migrazioak eta seeder-a
├── docker/                  nginx eta PHP-ren konfigurazioa Docker-erako
├── lang/                    Itzulpenak (eu, es, en)
├── public/                  Web-erroa: CSS, JS, irudiak eta index.php
├── resources/views/         Blade bistak
├── routes/web.php           Web bideak
├── security/                Segurtasun-probak eta txostenak
├── tests/                   PHPUnit testak
├── compose.yaml             Docker Compose
├── Dockerfile               Docker irudiak (app eta web)
├── .env.example             Konfigurazio-adibidea, lokalerako
└── .env.docker.example      Konfigurazio-adibidea, Docker-erako
```
