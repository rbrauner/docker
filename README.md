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

### Caddy and caddy labels

```bash
caddy trust
```

## Resolve containers by name from the host (DNS Proxy Server)

[`network/dns-proxy-server`](network/dns-proxy-server) runs [`defreitas/dns-proxy-server`](https://github.com/mageddo/dns-proxy-server), a DNS server backed by the Docker socket. Once the host uses it as a DNS resolver, you can connect to any running container by its container name with a `.docker` suffix (e.g. `mysql9.docker`) directly from host tools like DBeaver, without that container publishing its port to the host — just remove the container's `ports:` section in its `compose.yaml` and keep it on `shared-network`.

```bash
cd network/dns-proxy-server
docker compose up -d
```

Optional admin UI (to add custom DNS entries): https://dns-proxy-server.localhost

### Linux

#### NetworkManager

Check using:

```bash
nmcli -t -f GENERAL.CONNECTION,IP4.DNS device show <your-interface>
```

Enable using:

```bash
sudo nmcli connection modify "<connection-name>" ipv4.dns "127.0.0.1" ipv4.ignore-auto-dns yes
sudo nmcli connection up "<connection-name>"
```

Revert using:

```bash
sudo nmcli connection modify "<connection-name>" ipv4.ignore-auto-dns no ipv4.dns ""
sudo nmcli connection up "<connection-name>"
```

##### openvpn3 (CLI client)

Enable using:

```bash
sudo openvpn3-admin netcfg-service --config-set systemd-resolved 1
openvpn3 config-manage --config <your-config-name> --dns-scope tunnel
```

Revert using:

```bash
sudo openvpn3-admin netcfg-service --config-set systemd-resolved 0
openvpn3 config-manage --config <your-config-name> --dns-scope global
```

### macOS

Enable using:

```bash
networksetup -listallnetworkservices
networksetup -setdnsservers Wi-Fi 127.0.0.1
brew install chipmk/tap/docker-mac-net-connect
sudo brew services start chipmk/tap/docker-mac-net-connect
```

Revert using:

```bash
networksetup -setdnsservers Wi-Fi Empty
sudo brew services stop chipmk/tap/docker-mac-net-connect
brew uninstall chipmk/tap/docker-mac-net-connect
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
