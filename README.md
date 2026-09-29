# Docker

## Requirements

- docker
- hadolint
- prettier

## Make shared network

```bash
docker network create shared-network
```

## Run container

```bash
cd xxx/yyy
docker compose up -d
```

## Reverse proxy - certificates

### Traefik

```bash
mkcert -install
mkcert -cert-file network/traefik/certs/local-cert.pem -key-file network/traefik/certs/local-key.pem \
    "adminer.localhost" "*.adminer.localhost" \
    "affine.localhost" "*.affine.localhost" \
    "bookstack.localhost" "*.bookstack.localhost" \
    "buggregator.localhost" "*.buggregator.localhost" \
    "cloudbeaver.localhost" "*.cloudbeaver.localhost" \
    "crontab-ui.localhost" "*.crontab-ui.localhost" \
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

### Caddy and caddy labels

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
