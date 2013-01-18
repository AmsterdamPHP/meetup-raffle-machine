<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;

use Symfony\Component\Yaml\Yaml;
use Raffle\Event\EventService;
use Raffle\User\UserService;

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

$app['redis'] = new Predis\Client();

$app['meetup'] = new MeetupKeyAuthConnection($app['config']['meetup_api_key']);
$app['users']  = new UserService($app['meetup'], $app['redis']);
$app['events'] = new EventService($app['meetup'], $app['users'], $app['redis']);


return $app;
