FROM debian:stable-slim AS packages
# hadolint ignore=DL3008
RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends bash tzdata git vim curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

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
RUN groupadd -g "${GID}" "${GROUP}" \
    && useradd -u "${UID}" -g "${GID}" -m "${USER}" -l
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
