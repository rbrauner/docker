FROM alpine

# packages
RUN apk update
RUN apk upgrade
RUN apk add \
    bash \
    curl \
    fish \
    git \
    gnupg \
    neovim \
    openssh \
    vim \
    zsh

# user
RUN adduser -D dev
USER dev
WORKDIR /home/dev

# entrypoint
ENTRYPOINT [ "tail", "-f", "/dev/null" ]
