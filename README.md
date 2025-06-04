# Docker

## Containers

- `reverse-proxy` - reverse proxy containers
- `db-client` - database client containers
- `db-server` - database server containers
- `other` - other containers

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
    "reverse-proxy.localhost" "*.reverse-proxy.localhost" \
    "portainer.localhost" "*.portainer.localhost" \
    "mailpit.localhost" "*.mailpit.localhost" \
    "homepage.localhost" "*.homepage.localhost" \
    "open-webui.localhost" "*.open-webui.localhost" \
    "adminer.localhost" "*.adminer.localhost" \
    "pgadmin.localhost" "*.pgadmin.localhost" \
    "phpmyadmin.localhost" "*.phpmyadmin.localhost" \
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
