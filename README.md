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
    "mariadb.localhost" "*.mariadb.localhost" \
    "mysql.localhost" "*.mysql.localhost" \
    "mysql5.localhost" "*.mysql5.localhost" \
    "percona.localhost" "*.percona.localhost" \
    "percona5.localhost" "*.percona5.localhost" \
    "postgres.localhost" "*.postgres.localhost" \
    "redis.localhost" "*.redis.localhost" \
    "valkey.localhost" "*.valkey.localhost" \
    "rabbitmq.localhost" "*.rabbitmq.localhost" \
    "mailserver.localhost" "*.mailserver.localhost" \
    "adminer.localhost" "*.adminer.localhost" \
    "pgadmin.localhost" "*.pgadmin.localhost" \
    "phpmyadmin.localhost" "*.phpmyadmin.localhost" \
    "mailpit.localhost" "*.mailpit.localhost" \
    "cron-client.localhost" "*.cron-client.localhost" \
    "affine.localhost" "*.affine.localhost" \
    "bookstack.localhost" "*.bookstack.localhost" \
    "homepage.localhost" "*.homepage.localhost" \
    "joplin.localhost" "*.joplin.localhost" \
    "macos.localhost" "*.macos.localhost" \
    "ollama.localhost" "*.ollama.localhost" \
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
