<?php
namespace Raffle\User;

class UserService
{
    const RAW_GRAVATAR_URL = "http://www.gravatar.com/avatar/%s%s";

    /**
     * @var array
     */
    protected $meetupConfig;

    /**
     * @var \MeetupRsvps
     */
    protected $rsvpApi;

    /**
     * @var \Predis\Client
     */
    protected $redisApi;

    /**
     * @param array $meetupConfig
     * @param \Predis\Client $redis
     */
    public function __construct($meetupConfig, $redis)
    {
        $this->meetupConfig = $meetupConfig;
        $this->redisApi     = $redis;

        $this->rsvpApi = new \MeetupRsvps($this->meetupConfig);
    }

    public function getUser($id)
    {
        //TODO
    }

    /**
     * Builds a list of users at an event, pulling from various sources.
     *
     * @param string $id
     * @return UserEntity[]
     */
    public function getEventUsers($id)
    {
        $rsvps = $this->rsvpApi->getRsvps(array('event_id' => $id, 'rsvp' => 'yes'));
        $userList = $this->redisApi->lrange('event:'.$id, 0, 1000);

        $meetupBuilder = new \Raffle\User\Builder\MeetupUser();
        $rsvpUsers = $meetupBuilder->buildUserList($rsvps);

        $redisBuilder = new \Raffle\User\Builder\RedisUser();
        $redisUsers = $redisBuilder->buildUserList($userList);

        return array_merge($rsvpUsers, $redisUsers);
    }

    /**
     * Creates a new user based on email/name and stores to Redis
     *
     * @param string $name
     * @param string $email
     *
     * @return UserEntity
     */
    public function createNonMeetupUser($name, $email)
    {
        $emailHash = md5($email);
        $userId    = 'user:' . $emailHash;

        $user = new UserEntity();
        $user->setId('redis:' . $userId);
        $user->setEmail($email);
        $user->setName($name);
        $user->setPhoto(sprintf(self::RAW_GRAVATAR_URL, $emailHash, '?s=200'));
        $user->setThumbnail(sprintf(self::RAW_GRAVATAR_URL, $emailHash, '?s=80'));

        $this->redisApi->set($userId, json_encode($user));

        return $user;
    }
}
