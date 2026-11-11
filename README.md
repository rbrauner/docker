# Docker

Local development stack. Every directory with a `compose.yaml` is a standalone compose project. All projects share one external network (`shared-network`) and one external home volume (`shared-home`), so containers from different projects can reach each other by container name (e.g. an app connects to `postgres18` or `mariadb12`).

## Requirements

- docker
- make
- python3
- mkcert
- hadolint
- shellcheck
- prettier

## Prepare

### 1. Create AGENTS.md

```bash
cp AGENTS.example.md AGENTS.md
```

### 2. Make shared network

```bash
docker network create shared-network
```

### 3. Make shared home volume

```bash
cd other/shared-home
docker compose up -d
```

### 4. Make traefik certificates

```bash
mkcert -install
mkcert -cert-file network/traefik/certs/local-cert.pem -key-file network/traefik/certs/local-key.pem \
    "adminer.localhost" "*.adminer.localhost" \
    "affine.localhost" "*.affine.localhost" \
    "bookstack.localhost" "*.bookstack.localhost" \
    "buggregator.localhost" "*.buggregator.localhost" \
    "cloudbeaver.localhost" "*.cloudbeaver.localhost" \
    "crontab-ui.localhost" "*.crontab-ui.localhost" \
    "dns-proxy-server.localhost" "*.dns-proxy-server.localhost" \
    "example.localhost" "*.example.localhost" \
    "grafana.localhost" "*.grafana.localhost" \
    "homepage.localhost" "*.homepage.localhost" \
    "joplin.localhost" "*.joplin.localhost" \
    "listmonk.localhost" "*.listmonk.localhost" \
    "locust.localhost" "*.locust.localhost" \
    "macos.localhost" "*.macos.localhost" \
    "mailpit.localhost" "*.mailpit.localhost" \
    "mercure.localhost" "*.mercure.localhost" \
    "mockoon.localhost" "*.mockoon.localhost" \
    "mockserver.localhost" "*.mockserver.localhost" \
    "open-webui.localhost" "*.open-webui.localhost" \
    "pgadmin.localhost" "*.pgadmin.localhost" \
    "phpmyadmin.localhost" "*.phpmyadmin.localhost" \
    "portainer.localhost" "*.portainer.localhost" \
    "prism.localhost" "*.prism.localhost" \
    "prometheus.localhost" "*.prometheus.localhost" \
    "rabbitmq.localhost" "*.rabbitmq.localhost" \
    "scripts-php-disk-space-info.localhost" "*.scripts-php-disk-space-info.localhost" \
    "scripts-php-opcache-reset.localhost" "*.scripts-php-opcache-reset.localhost" \
    "scripts-php-phpinfo.localhost" "*.scripts-php-phpinfo.localhost" \
    "scripts-php-pwd.localhost" "*.scripts-php-pwd.localhost" \
    "traefik.localhost" "*.traefik.localhost" \
    "whoami.localhost" "*.whoami.localhost" \
    "wiremock.localhost" "*.wiremock.localhost"
```

### 5. Run traefik

```bash
cd network/traefik
docker compose up -d
```

## Run container

```bash
cd xxx/yyy
docker compose up -d
```

## Database backups

Each database server mounts a shared script as `/bin/backup.sh` (`db/server/_shared/backup-mysql.sh` or `backup-postgres.sh`). It dumps every non-system database to `${BACKUP_DIR:-~/data/backups/docker/<name>}` and removes dumps older than `BACKUP_RETENTION_DAYS` (default 7). Run it manually:

```bash
docker exec mysql8 /bin/backup.sh
```

or on a schedule: start [`other/cron`](other/cron) and uncomment the `ofelia` labels in the database `compose.yaml`.

## Caddy and caddy labels certificates

```bash
caddy trust
```

## Mercure - JWT keys

Generate two different random values, e.g.:

```bash
openssl rand -base64 32
openssl rand -base64 32
```

Put the values in `.env`:

```
MERCURE_PUBLISHER_JWT_KEY=xxx
MERCURE_SUBSCRIBER_JWT_KEY=yyy
```
