<?php

use DMS\Service\Meetup\MeetupKeyAuthClient;
use Raffle\MeetupOauthHandler;
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

// Meetup Oauth Handler
$app['meetup_oauth_handler'] = new MeetupOauthHandler($app);

// Meetup service
$config = array('key' => $app['config']['meetup_api_key']);
$app['meetup'] = new MeetupService(
    MeetupKeyAuthClient::factory($config),
    $app['config']['meetup_group']
);

// Random service
$app['random'] = new RandomService();

return $app;
