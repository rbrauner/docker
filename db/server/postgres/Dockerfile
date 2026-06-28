ARG DB_VERSION=18

FROM postgres:${DB_VERSION}-alpine AS packages
RUN apk update
RUN apk upgrade
RUN apk add bash tzdata git vim findutils curl
RUN apk cache clean

FROM packages AS environments
# args and envs
ARG TZ="Europe/Warsaw"
ENV TZ=${TZ}

FROM environments AS configs
# timezone and localtime
RUN echo $TZ > /etc/timezone
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime

FROM configs AS base
COPY ./docker/postgres/bin/backup.sh /bin/backup.sh
