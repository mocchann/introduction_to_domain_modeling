FROM php:8.2-apache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
