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

$app->post('/event/{id}/adduser', function ($id, Request $request) use ($app) {

    if ( ! $app['events']->addNonMeetupUser($id, $request->get('name'), $request->get('email'))) {
        return new Response("Error Adding user", $status = 500);
    }

    //return new JsonResponse(array("User Added."), 200);
    return new RedirectResponse("/event/".$id);
});

$app->get('/event/{id}/removeuser/{userId}', function ($id, $userId) use ($app) {

    /** @var $redis \Predis\Client */
    $redis = $app['redis'];

    //Add user to event
    $redis->lrem('event:'.$id, 0, $userId);

    return new JsonResponse(array("User Removed: $userId."), 200);
});

$app->post('/event/{id}/tagwinner', function ($id, Request $request) use ($app) {

        /** @var $redis \Predis\Client */
        $redis = $app['redis'];

        $user = array(
            'member' => array(
                'name' => $request->get('name'),
                'member_id' => $userId
            ),
            'member_photo' => array(
                'thumb_link' => sprintf(GRAVATAR_URL, $emailHash, '?s=80'),
                'highres_link' => sprintf(GRAVATAR_URL, $emailHash, '?s=200'),
            )
        );

        $winner = array(
            'user'  => $user,
            'prize' => $request->get('prize')
        );

        $winnerId = 'winner:' . $winner['prize'] . $id;

        $redis->set($winnerId, json_encode($winner));

        //Add user to event
        $redis->rpush('event_winner:' . $id, json_encode($winner));

        //return new JsonResponse(array("User Added."), 200);
        return new RedirectResponse("/event/".$id);
    });

$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    $page = 404 == $code ? '404.html' : '500.html';

    return new Response($app['twig']->render($page, array('code' => $code)), $code);
});
