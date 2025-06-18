############################################
# Node Image
############################################
FROM node:alpine AS node

WORKDIR app

COPY . /app

RUN npm install && npm run build

############################################
# Base Image
############################################

# Learn more about the Server Side Up PHP Docker Images at:
# https://serversideup.net/open-source/docker-php/
FROM serversideup/php:8.4-fpm-nginx AS base

# Switch to root before installing our PHP extensions
USER root
RUN apt update && apt install -y \
    ca-certificates \
    gnupg  \
    lsb-release  \
    && curl -fsSL https://www.postgresql.org/media/keys/ACCC4CF8.asc | \
    gpg --dearmor -o /usr/share/keyrings/postgresql.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/postgresql.gpg] \
    http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" | \
    tee /etc/apt/sources.list.d/pgdg.list \
    && apt update && apt install -y \
    postgresql-client \
    && install-php-extensions intl gd \
    && rm -rf /var/lib/apt/lists/*
USER www-data

############################################
# Development Image
############################################
FROM base AS development

# We can pass USER_ID and GROUP_ID as build arguments
# to ensure the www-data user has the same UID and GID
# as the user running Docker.
ARG USER_ID
ARG GROUP_ID

# Switch to root so we can set the user ID and group ID
USER root
RUN docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID  && \
    docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID --service nginx




# Switch back to the unprivileged www-data user
USER www-data

############################################
# CI image
############################################
FROM base AS ci

# Sometimes CI images need to run as root
# so we set the ROOT user and configure
# the PHP-FPM pool to run as www-data
USER root
RUN echo "user = www-data" >> /usr/local/etc/php-fpm.d/docker-php-serversideup-pool.conf && \
    echo "group = www-data" >> /usr/local/etc/php-fpm.d/docker-php-serversideup-pool.conf

############################################
# Production Image
############################################
FROM base AS deploy

ENV AUTORUN_ENABLED=true
ENV PHP_OPCACHE_ENABLE=1
ENV SHOW_WELCOME_MESSAGE=false

COPY --chown=www-data:www-data . /var/www/html
COPY --from=node --chown=www-data:www-data /app/public /var/www/html/public
COPY --chown=www-data:www-data --chmod=755 .docker/etc/entrypoint.d /etc/entrypoint.d

WORKDIR /var/www/html

RUN composer install --no-dev
