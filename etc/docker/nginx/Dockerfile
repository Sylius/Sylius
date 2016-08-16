FROM nginx:alpine

COPY ./etc/docker/nginx/default.conf /etc/nginx/conf.d/default.conf
ONBUILD COPY . /var/www

WORKDIR /var/www

EXPOSE 9000
