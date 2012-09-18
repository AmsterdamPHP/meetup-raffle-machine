<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

define('GRAVATAR_URL', "http://www.gravatar.com/avatar/%s%s");

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
    $meetupMembers = $rsvpApi->getRsvps(array('event_id' => $id, 'rsvp' => 'yes'));

    //Get Manual Users
    $userList = $app['redis']->lrange('event:'.$id, 0, 1000);
    $userList = array_map(function ($v) { return json_decode($v); }, $userList);

    $members = array_merge($meetupMembers, $userList);

    return $app['twig']->render('event.html.twig', array('event' => $event, 'members' => $members));
})
->bind('event')
;

$app->post('/event/{id}/adduser', function ($id, Request $request) use ($app) {

    /** @var $redis \Predis\Client */
    $redis = $app['redis'];

    //Store User
    $emailHash = md5($request->get('email'));
    $userId = 'user:' . $emailHash;

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

    $redis->set($userId, json_encode($user));

    //Add user to event
    $redis->rpush('event:'.$id, json_encode($user));

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
