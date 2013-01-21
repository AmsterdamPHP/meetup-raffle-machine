<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

define('GRAVATAR_URL', "http://www.gravatar.com/avatar/%s%s");

$app->get('/', function () use ($app) {

    $pastEvents     = $app['events']->loadEvents('amsterdamphp', 'past');
    $upcomingEvents = $app['events']->loadEvents('amsterdamphp', 'upcoming');

    $current = ($upcomingEvents[0]->getDate()->format('Ymd') == date('Ymd'))?  array_shift($upcomingEvents) : null;

    return $app['twig']->render(
        'index.html.twig',
        array(
            'past' => $pastEvents,
            'upcoming' => $upcomingEvents,
            'current' => $current
        )
    );
})
->bind('homepage')
;

$app->get('/event/{id}', function ($id) use ($app) {

    $event = $app['events']->loadEvent($id, true);

    return $app['twig']->render('event.html.twig', array('event' => $event));
})
->bind('event')
;

$app->post('/event/{id}/store-winner', function ($id, Request $request) use ($app) {

        $operation = $app['events']->storeWinner($id, $request->get('winner-id'), $request->get('prize'));

        if ( ! $operation) {
            return new JsonResponse(array("Error storing winner."), 500);
        }

        return new JsonResponse(array("Winner stored."), 200);
});

$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    $page = 404 == $code ? '404.html' : '500.html';

    return new Response($app['twig']->render($page, array('code' => $code)), $code);
});
