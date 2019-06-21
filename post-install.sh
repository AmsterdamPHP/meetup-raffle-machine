#!/usr/bin/env bash

# Install dependencies and build assets
su vagrant -c 'cd /vagrant && npm install && composer install --prefer-dist && node_modules/.bin/gulp'
