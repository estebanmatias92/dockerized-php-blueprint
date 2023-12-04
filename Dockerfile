# The base image for the container 
FROM php:8.1-cli as base
# Install PHP tools (composer, xdebug)
COPY --from=composer:2.4 /usr/bin/composer /usr/bin/composer
# Set this if you use composer as super user at all times like in docker containers
#ENV COMPOSER_ALLOW_SUPERUSER=1


#
# Development dependencies
#
FROM base as devdeps
# Install dev tools
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    && apt-get clean
# Install Docker tools (cli, buildx, compose) 
COPY --from=gloursdocker/docker / /


#
# Builder stage for Runtime
#     
FROM base as builder

WORKDIR /app

COPY ./composer.* .

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist


#
# VS Code Stage 
# (This is preferred to run as a Docker Dev Environment)
#
FROM devdeps as development
# Modifyble through cli args
ARG WORKDIR=/com.docker.devenvironments.code
ARG USER="vscode"
# Create and change user
RUN useradd -s /bin/bash -m $USER \
    && groupadd docker \
    && usermod -aG docker $USER
USER $USER
# Replace the host SSH exe with the WSL distro SSH exe
RUN git config --global --replace-all core.sshCommand "/usr/bin/ssh"

ENTRYPOINT ["sleep", "infinity"] 


#
# Production Target Stage 
# Normally called without specifying "target" in compose
#
FROM php:8.1-cli-alpine as production

WORKDIR /app

COPY --from=builder /app/vendor .
COPY . .

ENTRYPOINT ["php", "/public/index.php"]