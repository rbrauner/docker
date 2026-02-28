FROM debian:stable-slim AS start

FROM start AS packages
RUN apt update
RUN apt upgrade
RUN apt install \
    bash \
    build-base \
    ca-certificates \
    composer \
    curl \
    fish \
    git \
    gnupg \
    less \
    neovim \
    nodejs \
    npm \
    openssh \
    openssl \
    pipx \
    php84 \
    pnpm \
    python3 \
    tar \
    unzip \
    vim \
    wget \
    yarn \
    zip \
    zlib \
    zsh

FROM packages AS node-base
RUN npm uninstall -g yarn pnpm
RUN npm install -g corepack
RUN corepack enable

FROM node-base AS user
RUN adduser -D devbox
USER devbox
WORKDIR /home/devbox

ENV TZ=Europe/Warsaw
ENV LANG=en_US.UTF-8
ENV HOME=/home/devbox
ENV XDG_CACHE_HOME=${HOME}/.cache
ENV XDG_CONFIG_HOME=${HOME}/.config
ENV XDG_DATA_HOME=${HOME}/.local/share
ENV XDG_STATE_HOME=${HOME}/.local/state
ENV EDITOR=nvim
ENV VISUAL=nvim
ENV PAGER=less
ENV PNPM_HOME=${HOME}/.local/share/pnpm
ENV PHP_CS_FIXER_IGNORE_ENV=1

FROM user AS end
CMD ["bash"]
