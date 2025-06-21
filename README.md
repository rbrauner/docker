# Docker

## Make shared network

```bash
docker network create --driver bridge --subnet 10.0.0.0/8 --gateway 10.0.0.1 main
```

## Run container

```bash
cd xxx/yyy
docker compose up -d
```

## Reverse proxy - certificates

### Traefik

```bash
mkcert -cert-file reverse-proxy/traefik/certs/local-cert.pem -key-file reverse-proxy/traefik/certs/local-key.pem \
    "traefik.localhost" "*.traefik.localhost" \
    "portainer.localhost" "*.portainer.localhost" \
    "adminer.localhost" "*.adminer.localhost" \
    "pgadmin.localhost" "*.pgadmin.localhost" \
    "phpmyadmin.localhost" "*.phpmyadmin.localhost" \
    "mailserver.localhost" "*.mailserver.localhost" \
    "mailpit.localhost" "*.mailpit.localhost" \
    "rabbitmq.localhost" "*.rabbitmq.localhost" \
    "affine.localhost" "*.affine.localhost" \
    "bookstack.localhost" "*.bookstack.localhost" \
    "homepage.localhost" "*.homepage.localhost" \
    "joplin.localhost" "*.joplin.localhost" \
    "open-webui.localhost" "*.open-webui.localhost" \
    "example.localhost" "*.example.localhost"
mkcert -install
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
