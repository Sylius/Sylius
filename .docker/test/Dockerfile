FROM ghcr.io/sylius/sylius-php:8.2-fixuid-xdebug-alpine

USER root
RUN apk add --update;
RUN apk add nano \
    # for all test commands, they use makefile
        make \
    # for the integration tests, they need the frontend running
        nodejs npm yarn \
    ;

USER sylius
