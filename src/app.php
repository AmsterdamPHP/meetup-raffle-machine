<?php

use DMS\Service\Meetup\MeetupKeyAuthClient;
use Predis\Client;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;

use Symfony\Component\Yaml\Yaml;
use Raffle\MeetupService;
use Raffle\RandomService;

$app = new Application();

$app['config'] = Yaml::parse(__DIR__ . '/../config/parameters.yml');

$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());

// Twig Configuration
$app->register(new TwigServiceProvider(), array(
    'twig.path'    => array(__DIR__.'/../templates'),
    'twig.options' => array('cache' => __DIR__.'/../cache'),
));
$app['twig'] = $app->share($app->extend('twig', function($twig) {
    return $twig;
}));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// Session
$app->register(new Silex\Provider\SessionServiceProvider());

// Meetup service
$config = [
    'key' => '',
];

if (isset($app['config']['meetup_api_key'])) {
    $config['key'] = $app['config']['meetup_api_key'];
}

$app['meetup'] = new MeetupService(
    MeetupKeyAuthClient::factory($config),
    $app['config']['meetup_group'],
    new Client()
);

// Random service
$app['random'] = new RandomService();

return $app;
