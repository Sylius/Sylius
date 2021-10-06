ARG NODE_VERSION=10
FROM node:${NODE_VERSION}-alpine

WORKDIR /var/www/html

RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		g++ \
		gcc \
		git \
		make \
		python \
	;

WORKDIR /var/www/html