.DEFAULT_GOAL := help

.PHONY: help lint fix mocks

# ========
# = vars =
# ========
OPTIONS :=

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
		docker compose -f "$$f" config -q || { echo "invalid: $$f"; exit 1; }; \
	done

# Fix
fix:
	git ls-files '*compose.yaml' '*compose.*.yaml' | xargs prettier --write

# Build mock configs (mockoon, mockserver)
mocks:
	python3 mock/mockoon/build.py
	python3 mock/mockserver/build.py

-include Makefile.local.mk
