# Docker

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
mkcert -cert-file reverse-proxy/traefik/certs/local-cert.pem -key-file reverse-proxy/traefik/certs/local-key.pem \
    "traefik.localhost" "*.traefik.localhost" \
    "rabbitmq.localhost" "*.rabbitmq.localhost" \
    "adminer.localhost" "*.adminer.localhost" \
    "pgadmin.localhost" "*.pgadmin.localhost" \
    "phpmyadmin.localhost" "*.phpmyadmin.localhost" \
    "mailpit.localhost" "*.mailpit.localhost" \
    "crontab-ui.localhost" "*.crontab-ui.localhost" \
    "portainer.localhost" "*.portainer.localhost" \
    "affine.localhost" "*.affine.localhost" \
    "bookstack.localhost" "*.bookstack.localhost" \
    "homepage.localhost" "*.homepage.localhost" \
    "joplin.localhost" "*.joplin.localhost" \
    "macos.localhost" "*.macos.localhost" \
    "open-webui.localhost" "*.open-webui.localhost" \
    "example.localhost" "*.example.localhost"
```

### Caddy and caddy labels

```bash
caddy trust
```

## Other

### Ollama

```bash
docker exec -it ollama bash -c "ollama pull llama3.1:8b"
docker exec -it ollama bash -c "ollama pull qwen2.5-coder:32B"
```
