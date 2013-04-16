<?php

use Raffle\MeetupOauthHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGenerator;

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
$app->get('/event/{id}/checkin', function ($id, Request $request) use ($app) {

    /** @var MeetupOauthHandler $oauthHandler */
    $oauthHandler = $app['meetup_oauth_handler'];

    if ( ! $oauthHandler->hasSessionToken()) {
        $app['session']->set('redirect_url', $request->getUri());
        return $app->redirect($app['url_generator']->generate('meetup_oauth_authorize'));
    }

    $event = $app['meetup']->getEvent($id);

    return $app['twig']->render(
        'event_checkin.html.twig',
        array('event' => $event)
    );
})->bind('event_checkin');

// Checks a user into an event
$app->post('/user/checkin', function (Request $request) use ($app) {

    /** @var MeetupOauthHandler $oauthHandler */
    $oauthHandler = $app['meetup_oauth_handler'];
    $meetup       = $oauthHandler->getOauthMeetupService();

    $userId = $request->get('user_id');
    $eventId = $request->get('event_id');

    $operation = $meetup->checkUserIn($eventId, $userId);

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

// Oauth Handshake
$app->get('/oauth/authorize', function (Request $request) use ($app) {

        /** @var MeetupOauthHandler $oauthHandler */
        $oauthHandler = $app['meetup_oauth_handler'];

        $oauthHandler->clearSessionToken();

        $client = $oauthHandler->getOauthMeetupService()->getClient();

        $callback = $app['url_generator']->generate('meetup_oauth_callback', array(), UrlGenerator::ABSOLUTE_URL);
        $tokenResponse = $client->getRequestToken(array(
                'oauth_callback' => $callback
            ));

        $oauthHandler->setSessionToken($tokenResponse['oauth_token'], $tokenResponse['oauth_token_secret']);

        return $app->redirect('http://www.meetup.com/authorize/?oauth_token=' . $tokenResponse['oauth_token']);
    })->bind('meetup_oauth_authorize');

// Oauth Callback
$app->get('/oauth/callback', function (Request $request) use ($app) {

        /** @var MeetupOauthHandler $oauthHandler */
        $oauthHandler = $app['meetup_oauth_handler'];
        $client = $oauthHandler->getOauthMeetupService()->getClient();

        $response = $client->getAccessToken($request->query->all());

        $oauthHandler->setSessionToken($response['oauth_token'], $response['oauth_token_secret']);

        $redirect = $app['session']->get('redirect_url') ?: $app['url_generator']->generate('homepage');

        return $app->redirect($redirect);
})->bind('meetup_oauth_callback');
