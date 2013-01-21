<?php
namespace Raffle\User;

class UserService
{
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
}
