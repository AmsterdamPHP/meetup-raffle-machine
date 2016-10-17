#!/usr/bin/env bash

# Install Ruby packages
sudo gem install compass --no-ri --no-rdoc
sudo gem install susy --no-ri --no-rdoc

# Install dependencies and build assets
su vagrant -c 'cd /vagrant && npm install && composer install --prefer-dist && node_modules/.bin/gulp'
