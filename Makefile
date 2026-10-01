.DEFAULT_GOAL := help

.PHONY: help lint fix

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
	hadolint */**/Dockerfile
	prettier */**/{compose,compose.*}.yaml --check

# Fix
fix:
	prettier */**/{compose,compose.*}.yaml --write

-include Makefile.local.mk
