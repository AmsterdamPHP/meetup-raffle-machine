<?php
namespace Deployer;

require 'recipe/composer.php';

// Set configurations
set('repository', 'git@github.com:AmsterdamPHP/meetup-raffle-machine.git');
set('shared_files', ['config/parameters.yml']);
set('shared_dirs', ['cache', 'logs']);
set('writable_dirs', []);

// Configure servers
server('production', 'amsterdamphp.nl')
    ->user('phpamst01')
    ->identityFile()
    ->env('deploy_path', '/data/www/raffles.amsterdamphp.nl');

after('deploy:update_code', 'deploy:shared');
