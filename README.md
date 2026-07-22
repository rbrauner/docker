# Docker

## Requirements

- docker
- hadolint
- prettier

## Make shared network

```bash
docker network create --internal shared-network-internal
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
mkcert -cert-file reverse-proxy/traefik/certs/local-cert.pem -key-file reverse-proxy/traefik/certs/local-key.pem \
    "adminer.localhost" "*.adminer.localhost" \
    "affine.localhost" "*.affine.localhost" \
    "bookstack.localhost" "*.bookstack.localhost" \
    "crontab-ui.localhost" "*.crontab-ui.localhost" \
    "example.localhost" "*.example.localhost" \
    "grafana.localhost" "*.grafana.localhost" \
    "homepage.localhost" "*.homepage.localhost" \
    "joplin.localhost" "*.joplin.localhost" \
    "listmonk.localhost" "*.listmonk.localhost" \
    "locust.localhost" "*.locust.localhost" \
    "macos.localhost" "*.macos.localhost" \
    "mailpit.localhost" "*.mailpit.localhost" \
    "mockserver.localhost" "*.mockserver.localhost" \
    "open-webui.localhost" "*.open-webui.localhost" \
    "pgadmin.localhost" "*.pgadmin.localhost" \
    "phpmyadmin.localhost" "*.phpmyadmin.localhost" \
    "portainer.localhost" "*.portainer.localhost" \
    "rabbitmq.localhost" "*.rabbitmq.localhost" \
    "traefik.localhost" "*.traefik.localhost" \
    "whoami.localhost" "*.whoami.localhost" \
    "wiremock.localhost" "*.wiremock.localhost"
```

### Caddy and caddy labels

```bash
caddy trust
```
