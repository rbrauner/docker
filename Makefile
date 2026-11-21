.DEFAULT_GOAL := help

.PHONY: help lint fix mocks certs init up down ps list

# ========
# = vars =
# ========
OPTIONS :=
CERTS_DIR := network/traefik/certs
# directories scanned for *.localhost domains (extend in Makefile.local.mk: CERTS_SCAN_DIRS += ~/projects)
CERTS_SCAN_DIRS := .
# domains not found in any scanned file (extend in Makefile.local.mk: CERTS_EXTRA_DOMAINS += foo.localhost)
CERTS_EXTRA_DOMAINS := example.localhost whoami.localhost
# compose project directory for up/down (e.g. make up S=db/server/postgres18)
S :=
SHARED_NETWORK_NAME ?= shared-network

# ================
# = common tasks =
# ================

# Show available targets
help:
	@awk '/^# / { c = substr($$0, 3); next } /^[a-z][a-z-]*:/ { split($$1, a, ":"); if (c) printf "  %-14s %s\n", a[1], c; c = "" }' $(MAKEFILE_LIST)

# Lint
lint:
	git ls-files '*Dockerfile' | xargs hadolint
	git ls-files '*.sh' | xargs shellcheck
	git ls-files '*compose.yaml' '*compose.*.yaml' | xargs prettier --check
	for f in $$(git ls-files '*compose.yaml' '*compose.*.yaml' | grep -v override-example); do \
		env="$$(dirname "$$f")/.env.example"; \
		docker compose -f "$$f" $$([ -f "$$env" ] && echo --env-file "$$env") config -q || { echo "invalid: $$f"; exit 1; }; \
	done

# Fix
fix:
	git ls-files '*compose.yaml' '*compose.*.yaml' | xargs prettier --write

# Build mock configs (mockoon, mockserver)
mocks:
	python3 mock/mockoon/build.py
	python3 mock/mockserver/build.py

# Generate traefik certificates for every *.localhost domain found in CERTS_SCAN_DIRS
certs:
	domains=$$( { find $(CERTS_SCAN_DIRS) \( -name .git -o -name node_modules -o -name vendor \) -prune -o -type f \
			\( -name '*compose*.yaml' -o -name '*compose*.yml' -o -name '.env' -o -name '.env.*' \) -print0 \
			| xargs -0 -r grep -ohE '[a-z0-9_][a-z0-9_-]*(\.[a-z0-9_-]+)*\.localhost\b'; \
		printf '%s\n' $(CERTS_EXTRA_DOMAINS); } | sort -u ); \
	echo "$$domains"; \
	mkcert -cert-file $(CERTS_DIR)/local-cert.pem -key-file $(CERTS_DIR)/local-key.pem \
		$$(for d in $$domains; do printf '%s *.%s ' "$$d" "$$d"; done)

# One-time setup: AGENTS.md, local CA, shared network and home volume, certificates, traefik
init:
	[ -f AGENTS.md ] || cp AGENTS.example.md AGENTS.md
	mkcert -install
	docker network inspect $(SHARED_NETWORK_NAME) >/dev/null 2>&1 || docker network create $(SHARED_NETWORK_NAME)
	cd other/shared-home && docker compose up -d
	$(MAKE) certs
	cd network/traefik && docker compose up -d

# Start a project: make up S=db/server/postgres18
up:
	@[ -f "$(S)/compose.yaml" ] || { echo "usage: make up S=<dir with compose.yaml>"; exit 1; }
	cd $(S) && docker compose up -d $(OPTIONS)

# Stop a project: make down S=db/server/postgres18
down:
	@[ -f "$(S)/compose.yaml" ] || { echo "usage: make down S=<dir with compose.yaml>"; exit 1; }
	cd $(S) && docker compose down $(OPTIONS)

# Show containers (running and stopped) started from this repo
ps:
	@docker ps -a --format '{{.Label "com.docker.compose.project.working_dir"}}\t{{.Names}}\t{{.Status}}' \
		| awk -F '\t' -v root="$(CURDIR)/" 'index($$1, root) == 1 { printf "%-32s %-24s %s\n", substr($$1, length(root) + 1), $$2, $$3 }' | sort

# List projects with their default domains
list:
	@for f in $$(git ls-files '*compose.yaml'); do \
		domains=$$(grep -ohE '[a-z0-9_][a-z0-9_-]*(\.[a-z0-9_-]+)*\.localhost' "$$f" | sort -u | tr '\n' ' '); \
		printf "%-32s %s\n" "$$(dirname "$$f")" "$$domains"; \
	done

-include Makefile.local.mk
