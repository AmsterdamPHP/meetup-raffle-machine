<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$app->get('/', function () use ($app) {

    $m = new MeetupEvents($app['meetup']);
    $events = $m->getEvents( array( 'group_urlname' => 'amsterdamphp', 'status' => 'past,upcoming')  );

    return $app['twig']->render('index.html.twig', array('events' => $events));
})
->bind('homepage')
;

$app->get('/event/{id}', function ($id) use ($app) {

    $eventApi = new MeetupEvents($app['meetup']);
    $event = $eventApi->getEvent($id, array());

    $rsvpApi = new MeetupRsvps($app['meetup']);
    $meetupMembers = $rsvpApi->getRsvps(array('event_id' => $id));

    //TODO
    //$manualMembers = get from DB

    $idFilter = function ($v) { return $v['member']['member_id']; };

    $ids = array_map($idFilter, $meetupMembers);
    $ids = array_merge($ids, $ids, $ids, $ids);
    shuffle($ids);

    $members = $meetupMembers;

    return $app['twig']->render('event.html.twig', array('event' => $event, 'members' => $members, 'ids' => json_encode($ids)));
})
->bind('event')
;

$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    $page = 404 == $code ? '404.html' : '500.html';

    return new Response($app['twig']->render($page, array('code' => $code)), $code);
});
