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

// Check-in page for Event
$app->get('/event/{id}/checkin', function ($id) use ($app) {
    $event = $app['meetup']->getEvent($id);

    return $app['twig']->render(
        'event_checkin.html.twig',
        array('event' => $event)
    );
})->bind('event_checkin');

// Checks a user into an event
$app->post('/user/checkin', function (Request $request) use ($app) {

    $userId = $request->get('user_id');
    $eventId = $request->get('event_id');

    $operation = $app['meetup']->checkUserIn($eventId, $userId);

    $httpCode = ($operation)? 200:500;

    return new Response(
        json_encode(array('result' => $operation)),
        $httpCode,
        array('Content-Type' => 'application/json')
    );
})->bind('user_checkin');

// Error page
$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    $page = 404 == $code ? '404.html' : '500.html';

    return new Response($app['twig']->render($page, array('code' => $code)), $code);
});
