# Docker

Local development stack. Every directory with a `compose.yaml` is a standalone compose project. All projects share one external network (`shared-network`) and one external home volume (`shared-home`), so containers from different projects can reach each other by container name (e.g. an app connects to `postgres18` or `mariadb13`).

## Requirements

- docker
- make
- python3
- mkcert
- hadolint
- shellcheck
- prettier

## Prepare

### 1. Create `Makefile.local.mk` and set variables e.g.

```Makefile
CERTS_SCAN_DIRS += ~/data/workspace
```

### 2. Run `make init`

```bash
make init
```

## Makefile commands

```bash
make help
make up S=db/server/postgres18
make down S=db/server/postgres18
make ps
make list
```

## Configuration

Every project reads its own `.env` (next to `compose.yaml`).

### Common variables

| Variable                                                | Default                                           | Meaning                                                             |
| ------------------------------------------------------- | ------------------------------------------------- | ------------------------------------------------------------------- |
| `<NAME>_VERSION`                         | shortest published version tag (e.g. `3`, `13.3`) | image tag                                                           |
| `DB_VERSION`                         | shortest published version tag (e.g. `3`, `13.3`) | image tag (only in `db/server`                                                           |
| `DOMAIN`                                                | `<project>.localhost`                             | hostname and traefik router rule                                    |
| `<NAME>_BIND_IP`                                        | `127.0.0.1`                                       | host IP for published ports (commented `ports:` blocks and traefik) |
| `<NAME>_PORT`                    | service port                                      | published host port                                                 |
| `TRAEFIK_PORT_<PORT>`                    | -                                      | published host port for Traefik                                                 |
| `TZ`                                                    | `Europe/Warsaw`                                   | container timezone                                                  |
| `SHARED_NETWORK_NAME`                                   | `shared-network`                                  | external network shared by all projects                             |
| `SHARED_HOME_VOLUME`                                    | `shared-home`                                     | external home volume shared by all projects                         |
| `APP_USER`                                              | `app`                                             | home directory name inside the shared volume                        |
| `UID`                                              | `1000`                                             | user id for container                        |
| `GID`                                              | `1000`                                             | group id for container                        |
| `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_ROOT_PASSWORD` | `default`, `root`, `secret`                       | database servers                                                    |

## Database backups

Each database server mounts a shared script as `/bin/backup.sh` (`db/server/_shared/backup-mysql.sh` or `backup-postgres.sh`). It dumps every non-system database to `${BACKUP_DIR:-~/data/backups/docker/<name>}` and removes dumps older than `BACKUP_RETENTION_DAYS` (default 30).

### Create backup:

```bash
docker exec -it postgres18 bash -c "/bin/backup.sh"
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
