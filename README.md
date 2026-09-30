# t5-erronka1-ziber

Zibersegurtasun-arloko proiektua (1. erronka): ZiberEibar-eko ikastaroak kudeatzeko web-aplikazio baten diseinua, garapena, inplementazioa eta segurtasun-azterketa.

## Edukia

| Karpeta | Zer da |
|---|---|
| [`School-Managment/`](School-Managment/) | **ZiberEibar** web-aplikazioa (Laravel): ikasleak, ikastaroak eta matrikulak kudeatzeko sistema. Ezaugarriak, arkitektura, instalazioa eta Docker bidezko inplementazioa bere [README](School-Managment/README.md) fitxategian daude. |
| [`School-Managment/security/`](School-Managment/security/) | Segurtasun-probak: 33 egiaztapeneko script-a (OWASP Top 10), txostenak eta aurkitutako eta konpondutako ahultasunen zerrenda. Ikus [security/README.md](School-Managment/security/README.md). |

## Hasiera azkarra

Behar dena: PHP 8.3 edo berriagoa eta Composer.

```bash
cd School-Managment
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Ondoren, ireki <http://localhost:8000> eta hasi saioa `admin@educenter.es` / `password` kontuarekin. Proba-kontu hauek lokalean bakarrik existitzen dira.

Zerbitzari batean Docker-ekin inplementatzeko, ikus [School-Managment/README.md](School-Managment/README.md#inplementazioa-docker-ekin).
