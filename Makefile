.DEFAULT_GOAL := help

.PHONY: help init certs lint fix up down ps list mocks

# [[ Vars ]]
CERTS_DIR := network/traefik/certs
CERTS_EXTRA_DOMAINS := example.localhost whoami.localhost
CERTS_SCAN_DIRS := .
SHARED_NETWORK_NAME ?= shared-network

# [[ Targets ]]

# Show available targets
help:
	@awk '/^# / { c = substr($$0, 3); next } /^[a-z][a-z-]*:/ { split($$1, a, ":"); if (c) printf "  %-14s %s\n", a[1], c; c = "" }' $(MAKEFILE_LIST)

# Init
init:
	[ -f AGENTS.md ] || cp AGENTS.example.md AGENTS.md
	mkcert -install
	docker network inspect $(SHARED_NETWORK_NAME) >/dev/null 2>&1 || docker network create $(SHARED_NETWORK_NAME)
	cd other/shared-home && docker compose up -d
	$(MAKE) certs
	cd network/traefik && docker compose up -d

# Generate traefik certificates for every *.localhost domain found in CERTS_SCAN_DIRS
certs:
	domains=$$( { find $(CERTS_SCAN_DIRS) \( -name .git -o -name node_modules -o -name vendor \) -prune -o -type f \
		\( -name '*compose*.yaml' -o -name '*compose*.yml' -o -name '.env' -o -name '.env.*' \) -print0 \
		| xargs -0 -r grep -ohE '[a-z0-9_][a-z0-9_-]*(\.[a-z0-9_-]+)*\.localhost\b'; \
		printf '%s\n' $(CERTS_EXTRA_DOMAINS); } | sort -u ); \
	echo "$$domains"; \
	mkcert -cert-file $(CERTS_DIR)/local-cert.pem -key-file $(CERTS_DIR)/local-key.pem \
		$$(for d in $$domains; do printf '%s *.%s ' "$$d" "$$d"; done)

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

# Build mock configs
mocks:
	python3 mock/mockoon/build.py
	python3 mock/mockserver/build.py

-include Makefile.local.mk
