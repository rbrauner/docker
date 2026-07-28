# https://just.systems

# Default
set default-list := true

# Lint
lint:
    hadolint */**/Dockerfile
    prettier */**/{compose,compose.*}.yaml --check

# Fix
fix:
    prettier */**/{compose,compose.*}.yaml --write
