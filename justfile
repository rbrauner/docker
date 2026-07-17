# https://just.systems

# Default
set default-list := true

lint:
    hadolint */**/Dockerfile
    prettier */**/{compose,compose.*}.yaml --check

fix:
    prettier */**/{compose,compose.*}.yaml --write
