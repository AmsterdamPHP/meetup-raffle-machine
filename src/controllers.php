<?php

use Predis\Client;
use Raffle\MeetupOauthHandler;
use Raffle\RandomService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Add type hint for IDEs, since this file is included
 *
 * @var Silex\Application $app
 */

// Event index
$app->get('/', function (Request $request) use ($app) {
    $cacheBusting = filter_var($request->get('cache_busting', false), FILTER_VALIDATE_BOOLEAN);

    /** @var \Raffle\MeetupService $meetupService */
    $meetupService = $app['meetup'];

    return $app['twig']->render(
        'index.html.twig',
        array('meetups' => $meetupService->getPresentAndPastEvents($cacheBusting))
    );
})->bind('homepage');

// Specific event
$app->get('/event/{id}', function ($id) use ($app) {
    $event = $app['meetup']->getEvent($id);

    /** @var RandomService $randomService */
    $randomService = $app['random'];

    $client = new Client();
    $checkins = array_filter($client->lrange('checkin_'.$id, 0, 300));

    $winners = (count($checkins) > 1)? $randomService->getRandomNumbers(count($checkins)) : array(0);

    return $app['twig']->render(
        'event.html.twig',
        array('event' => $event, 'winners' => $winners, 'checkins' => $checkins)
    );
})->bind('event');

// Check-in page for Event
$app->get('/event/{id}/checkin', function ($id, Request $request) use ($app) {

    $event = $app['meetup']->getEvent($id);
    $client = new Client();
    $checkins = array_filter($client->lrange('checkin_'.$id, 0, 300));

    return $app['twig']->render(
        'event_checkin.html.twig',
        array('event' => $event, 'checkins' => $checkins)
    );
})->bind('event_checkin');

// Checks a user into an event
$app->post('/user/checkin', function (Request $request) use ($app) {

    $userId = $request->get('user_id');
    $eventId = $request->get('event_id');

    $client = new Client();
    $client->lpush('checkin_'.$eventId, $userId);

    return new Response(
        json_encode(array('result' => 'ok')),
        200,
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
$app->get('/oauth/authorize', function () use ($app) {

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
