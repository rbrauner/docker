# Docker

Local development stack: a set of standalone Compose projects sharing one external network (`shared-network`) and a shared home volume (`shared-home`, declared external by its consumers). Web services are exposed through Traefik (or Caddy) on `*.localhost` hostnames.

## Requirements

- Docker with the **Compose v2** plugin (`docker compose`)
- Python 3 (for `bin/init` and `bin/certs`)
- `mkcert`
- `just`
- `hadolint`, `shellcheck`, `prettier` - optional

## Quick start

```bash
bin/init
```

Run `just --list` to see the available recipes.

## Run a project

Every directory containing a `compose.yaml` is its own Compose project:

```bash
cd db/client/adminer
docker compose up -d
docker compose logs -f
```

Open `https://adminer.localhost`. TCP services are routed by Traefik `HostSNI` rules (e.g. Postgres on port 5432); direct `ports:` blocks are mostly commented out. Because all projects join the same network, containers can reach each other by container name.

Compose reads `.env` from the **current working directory**, so use the `cd <project> && docker compose ...` form.

## Configuration

There is no single global env file. Most variables are project-local and have defaults in the relevant `compose.yaml`.

| Variable                              | Default                                 | Notes                                                                                    |
| ------------------------------------- | --------------------------------------- | ---------------------------------------------------------------------------------------- |
| `<SERVICE>_VERSION`                   | per project (e.g. `TRAEFIK_VERSION=3`)  | Image tag; not a global variable.                                                        |
| `DB_VERSION`                          | per server (e.g. `19-trixie`, `13`)     | Image tag, only in `db/server/`.                                                         |
| `DOMAIN`                              | `<service>.localhost`                   | Container hostname and Traefik `Host` / `HostSNI` rule.                                  |
| `TZ`                                  | `Europe/Warsaw`                         | Container timezone and image build arg.                                                  |
| `SHARED_NETWORK_NAME`                 | `shared-network`                        | External network; must match in `bin/init` and every Compose project.                    |
| `SHARED_HOME_VOLUME`                  | `shared-home`                           | Named volume created by `other/shared-home`, used externally by other projects.          |
| `APP_USER`, `APP_GROUP`               | `app`                                   | Home directory/group used inside the shared volume.                                      |
| `UID`, `GID`                          | `1000`                                  | Ownership in `shared-home` and image builds; most `user:` lines are commented out.       |
| `<SERVICE>_BIND_IP`                   | `127.0.0.1`                             | Host IP for published ports (Traefik, Caddy, commented `ports:` blocks).                 |
| `TRAEFIK_PORT_<PORT>`                 | same as the container port              | `80`, `443`, `3306`, `5432`, `6379`, `25`, `465`, `587`, `993`, `5672`.                  |
| `DB_NAME`                             | `default`                               | Initial database for Postgres/pgvector and MySQL/MariaDB/Percona.                        |
| `DB_USER`                             | `root`                                  | Postgres/pgvector user; MySQL/MariaDB/Percona always use `root`.                         |
| `DB_PASSWORD`, `DB_ROOT_PASSWORD`     | `secret`                                | Postgres/Redis/Valkey password vs. MySQL/MariaDB/Percona root password.                  |
| `CADDY_INGRESS_NETWORKS`              | `shared-network`                        | Ingress network for `network/caddy-labels`; set it explicitly if you rename the network. |
| `CERTS_SCAN_DIRS_EXTRA`               | empty                                   | Extra directories for certificate discovery; read only from the root `.env`.             |
| `BACKUP_DIR`, `BACKUP_RETENTION_DAYS` | `~/data/backups/docker/<project>`, `30` | Host-side backup destination and retention in days.                                      |
| `MERCURE_PUBLISHER_JWT_KEY`           | required                                | Set in `message-broker/mercure/.env`; see below.                                         |
| `MERCURE_SUBSCRIBER_JWT_KEY`          | required                                | Set in `message-broker/mercure/.env`; must differ from the publisher key.                |

**Env scoping matters.** The root `.env` is parsed only by `bin/init` and `bin/certs`, which use `CERTS_SCAN_DIRS_EXTRA` and `SHARED_NETWORK_NAME` from it; Compose does not load it. To rename the shared network, set `SHARED_NETWORK_NAME` in **both** the root `.env` (so `bin/init` creates that network) and your exported shell environment or each project's `.env` (so Compose uses the same name). Exporting it alone has the opposite failure: `bin/init` creates `shared-network` while Compose looks for the custom one. All other Compose variables belong in the project's `.env` or in the exported shell environment.

## Certificates

Certificate generation scans this repository (plus `CERTS_SCAN_DIRS_EXTRA`) for lowercase `*.localhost` domains in `compose*.yaml`, `.env` and `.env.*` files, and adds `example.localhost` and `whoami.localhost` explicitly. Each domain gets both the bare name and a `*.domain` SAN. Generated certs are gitignored.

```bash
just certs                 # or: python3 bin/certs
cd network/traefik && docker compose restart
```

`just certs` only regenerates the cert files: it does not install the mkcert CA (only `bin/init` does) and does not restart Traefik. Browsers need the CA installed to trust HTTPS; Firefox may need its own trust store.

## Database backups

Postgres/pgvector and MySQL/MariaDB/Percona servers mount `db/server/_shared/backup-postgres.sh` or `backup-mysql.sh` as `/bin/backup.sh`. Redis and Valkey projects do not have a backup script.

```bash
docker exec -it postgres19 /bin/backup.sh
```

The script writes gzipped dumps to `/backup` inside the container, which is bind-mounted from `${BACKUP_DIR:-$HOME/data/backups/docker/postgres19}` on the host, and deletes dumps older than `BACKUP_RETENTION_DAYS` (default 30). Scheduling is off by default: the Ofelia labels in the database Compose files are commented out. `other/backup` is a separate volume-backup stack with its own cron schedule; `bin/init` does not start it.

## Mercure

```bash
cp message-broker/mercure/.env.example message-broker/mercure/.env
openssl rand -base64 32   # publisher key
openssl rand -base64 32   # subscriber key
```

Replace both `change-me` placeholders with the two different values. Compose only checks that the keys are non-empty, so identical placeholders will pass but are insecure. `MERCURE_CORS_ORIGINS` defaults to `*`, and `anonymous` access plus `publish_origins *` are hardcoded in `compose.yaml`; restrict what you can for anything beyond local experiments.

## Good to know

- Traefik and certificate rules assume `*.localhost` resolves locally; if your environment does not resolve wildcard subdomains, add the names you use to `/etc/hosts`.
- `apps/macos` requires `/dev/kvm` and `/dev/net/tun`, so it needs Linux with KVM and will not run on a typical Docker Desktop macOS host.
- To create a mailbox after starting `mail/server/mailserver`, see `mail/server/mailserver/README.md`.
- `just mocks` regenerates the mock configs from the `mock/` builders.
