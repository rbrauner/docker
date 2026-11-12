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

```bash
make bootstrap CERTS_SCAN_DIRS=". ~/data/workspace"
```

`make bootstrap` creates `AGENTS.md` (from `AGENTS.example.md`), installs the mkcert local CA, creates the shared network, starts `other/shared-home`, generates traefik certificates and starts `network/traefik`. Every step is safe to re-run.

## Run container

```bash
make up S=db/server/postgres18      # or: cd db/server/postgres18 && docker compose up -d
make down S=db/server/postgres18
make ps                             # containers started from this repo
make list                           # projects with their default *.localhost domains
```

Extra flags go through `OPTIONS`, e.g. `make up S=apps/joplin OPTIONS=--pull=always`.

## Configuration

Every project reads its own `.env` (next to `compose.yaml`, gitignored). Common variables:

| Variable                                                | Default                                           | Meaning                                                             |
| ------------------------------------------------------- | ------------------------------------------------- | ------------------------------------------------------------------- |
| `<NAME>_VERSION` / `DB_VERSION`                         | shortest published version tag (e.g. `3`, `13.3`) | image tag                                                           |
| `DOMAIN`                                                | `<project>.localhost`                             | hostname and traefik router rule                                    |
| `<NAME>_BIND_IP`                                        | `127.0.0.1`                                       | host IP for published ports (commented `ports:` blocks and traefik) |
| `<NAME>_PORT`, `TRAEFIK_PORT_<port>`                    | service port                                      | published host port                                                 |
| `TZ`                                                    | `Europe/Warsaw`                                   | container timezone                                                  |
| `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_ROOT_PASSWORD` | `default`, `root`, `secret`                       | database servers                                                    |
| `SHARED_NETWORK_NAME`                                   | `shared-network`                                  | external network shared by all projects                             |
| `SHARED_HOME_VOLUME`                                    | `shared-home`                                     | external home volume shared by all projects                         |
| `APP_USER`                                              | `app`                                             | home directory name inside the shared volume                        |

Apps that use a database (joplin, bookstack, listmonk, affine) connect to a server from another project (e.g. `postgres18`), so start it first - `depends_on` does not work across compose projects.

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

Copy `message-broker/mercure/.env.example` to `.env` and put the values there:

```
MERCURE_PUBLISHER_JWT_KEY=xxx
MERCURE_SUBSCRIBER_JWT_KEY=yyy
```
