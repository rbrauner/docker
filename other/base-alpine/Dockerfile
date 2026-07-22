FROM alpine:3 AS packages
# hadolint ignore=DL3018
RUN apk upgrade \
    && apk add --no-cache bash tzdata git vim curl

FROM packages AS environments
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
RUN addgroup -g "${GID}" -S "${GROUP}" \
    && adduser -u "${UID}" -D -S -G "${GROUP}" "${USER}"
# make sure `/var/www/html` exists and safe for git operations
RUN mkdir -p /var/www/html \
    && git config --global --add safe.directory /var/www/html
# working directory
WORKDIR /var/www/html

FROM configs AS base
USER ${UID}:${GID}
#USER root

FROM base AS dev
CMD ["/usr/bin/bash", "-c", "echo 'Hello dev!'"]

FROM base AS prod
CMD ["/usr/bin/bash", "-c", "echo 'Hello prod!'"]
