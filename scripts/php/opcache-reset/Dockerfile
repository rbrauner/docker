FROM dunglas/frankenphp:builder-trixie AS builder

# copy xcaddy in the builder image
COPY --from=caddy:builder /usr/bin/xcaddy /usr/bin/xcaddy

# CGO must be enabled to build FrankenPHP
RUN CGO_ENABLED=1 \
    XCADDY_SETCAP=1 \
    XCADDY_GO_BUILD_FLAGS="-ldflags='-w -s' -tags=nobadger,nomysql,nopgx" \
    CGO_CFLAGS=$(php-config --includes) \
    CGO_LDFLAGS="$(php-config --ldflags) $(php-config --libs)" \
    xcaddy build \
        --output /usr/local/bin/frankenphp \
        --with github.com/dunglas/frankenphp=./ \
        --with github.com/dunglas/frankenphp/caddy=./caddy/ \
        --with github.com/dunglas/caddy-cbrotli \
        # Mercure and Vulcain are included in the official build, but feel free to remove them
        --with github.com/dunglas/mercure/caddy \
        --with github.com/dunglas/vulcain/caddy
        # add extra Caddy modules here

FROM dunglas/frankenphp:trixie AS packages
# hadolint ignore=DL3008
RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends bash tzdata git vim curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

FROM packages AS extensions
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    opcache \
    @composer

FROM extensions AS environments
# args and envs
ARG TZ="Europe/Warsaw"
ARG USER="app"
ARG GROUP="app"
ARG UID=1000
ARG GID=1000
ENV TZ=${TZ}
ENV USER=${USER}
ENV GROUP=${GROUP}
ENV UID=${UID}
ENV GID=${GID}

FROM environments AS configs
# TZ and localtime
RUN echo "${TZ}" > /etc/TZ \
    && ln -snf "/usr/share/zoneinfo/${TZ}" /etc/localtime
# create user and group for app
# hadolint ignore=SC3028
RUN groupadd -g "${GID}" "${GROUP}" \
    && useradd -u "${UID}" -g "${GID}" -m "${USER}" -l
# make sure `/var/www/html` exists and safe for git operations
RUN mkdir -p /var/www/html \
    && git config --global --add safe.directory /var/www/html
# working directory
WORKDIR /var/www/html
# add additional capability to bind to port 80 and 443
RUN setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp
# give write access to /config/caddy and /data/caddy
RUN chown -R "${USER}:${USER}" /config/caddy /data/caddy
# replace the official binary by the one contained your custom modules
COPY --from=builder /usr/local/bin/frankenphp /usr/local/bin/frankenphp

FROM configs AS base

FROM base AS app
COPY ./configs/php.ini ${PHP_INI_DIR}/php.ini
COPY ./configs/Caddyfile /etc/frankenphp/Caddyfile
USER ${UID}:${GID}
