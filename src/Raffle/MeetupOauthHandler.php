<?php

namespace Raffle;

use DMS\Service\Meetup\MeetupOAuthClient;
use Silex\Application;
use Silex\Provider\SessionServiceProvider;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Security\Acl\Exception\SidNotLoadedException;

class MeetupOauthHandler
{
    const TOKEN_KEY = 'token';
    const TOKEN_SECRET_KEY = 'token_secret';

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Application
     */
    protected $app;

    public function __construct($app)
    {
        $this->session = $app['session'];
        $this->app     = $app;
    }

    /**
     * Gets a fully configured Oauth Client
     *
     * @return MeetupService
     */
    public function getOauthMeetupService()
    {
        $config = array(
            'consumer_key' => $this->app['config']['meetup_api_consumer_key'],
            'consumer_secret' => $this->app['config']['meetup_api_consumer_secret'],
            'token' => $this->session->get(self::TOKEN_KEY),
            'token_secret' => $this->session->get(self::TOKEN_SECRET_KEY),
        );

        $client = MeetupOAuthClient::factory($config);

        return new MeetupService($client, $this->app['config']['meetup_group']);
    }

    /**
     * Sets the token in the session
     *
     * @param $token
     * @param $tokenSecret
     */
    public function setSessionToken($token, $tokenSecret)
    {
        $this->session->set(self::TOKEN_KEY, $token);
        $this->session->set(self::TOKEN_SECRET_KEY, $tokenSecret);
    }

    /**
     * Clears any Oauth tokens from the session
     */
    public function clearSessionToken()
    {
        $this->session->remove(self::TOKEN_KEY);
        $this->session->remove(self::TOKEN_SECRET_KEY);
    }

    /**
     * Token already in session?
     *
     * @return bool
     */
    public function hasSessionToken()
    {
        return ($this->session->has(self::TOKEN_KEY) && $this->session->has(self::TOKEN_SECRET_KEY));
    }
}
