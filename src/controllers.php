<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Event index
$app->get('/', function () use ($app) {
    return $app['twig']->render(
        'index.html.twig',
        array('meetups' => $app['meetup']->getEvents())
    );
})->bind('homepage');

// Specific event
$app->get('/event/{id}', function ($id) use ($app) {
    $event = $app['meetup']->getEvent($id);
    $winners = $app['random']->getRandomNumbers(0, count($event['checkins']) - 1, 100);

    return $app['twig']->render(
        'event.html.twig',
        array('event' => $event, 'winners' => $winners)
    );
})->bind('event');

// Error page
$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    $page = 404 == $code ? '404.html' : '500.html';

    return new Response($app['twig']->render($page, array('code' => $code)), $code);
});
