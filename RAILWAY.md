# Deploy su Railway (Laravel 11)

Questa app e' pronta per Nixpacks tramite `nixpacks.toml`.

## 1) Servizi da creare in Railway

1. Crea un nuovo progetto Railway collegato a questo repository GitHub.
2. Aggiungi un servizio **MySQL**.
3. Aggiungi un **Volume** e montalo nel servizio web su path `/data`.

Opzionale:
- Redis (solo se vuoi queue/cache su Redis).

## 2) Variabili ambiente (servizio web)

Imposta almeno:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<tuo-dominio-o-url-railway>
APP_KEY=base64:...

LOG_CHANNEL=stderr
LOG_LEVEL=info

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_SECURE_COOKIE=true

DOCUMENTS_ROOT=/data/documents
RUN_DB_SEED=true
```

Note:
- `APP_KEY` va generata una volta (es. `php artisan key:generate --show` in locale) e copiata su Railway.
- Le variabili DB (`MYSQLHOST`, `MYSQLDATABASE`, ecc.) vengono valorizzate automaticamente dal plugin MySQL Railway.
- Dopo il primo deploy imposta `RUN_DB_SEED=false` per non rieseguire seed ad ogni riavvio.

## 3) Build e start

Railway usera' `nixpacks.toml`:
- build: `composer install`, `npm ci`, `npm run build`
- start:
  - `php artisan storage:link` (best effort)
  - `php artisan migrate --force`
  - seed opzionale con `RUN_DB_SEED=true`
  - avvio web server PHP su `$PORT`

## 4) Worker queue (consigliato)

Crea un secondo servizio dallo stesso repository (senza dominio), con comando start:

```bash
php artisan queue:work --queue=default --sleep=3 --tries=3 --timeout=90
```

Condividi le stesse variabili ambiente del web.

## 5) Scheduler (consigliato)

Crea un terzo servizio dallo stesso repository con comando start:

```bash
php artisan schedule:work
```

Serve per i comandi pianificati in `routes/console.php` (controllo scadenze documenti e invio promemoria email).

## 6) Dominio

1. Aggiungi dominio Railway o custom domain.
2. Aggiorna `APP_URL` con URL HTTPS definitivo.

## 7) Check post deploy

1. Login admin.
2. Upload documento e download (verifica persistenza su volume).
3. Esecuzione queue (`queue:work`) e schedule (`schedule:work`) senza errori.
