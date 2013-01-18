<?php
namespace Raffle\Event;

class EventEntity
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \DateTime;
     */
    protected $date;

    /**
     * @var array
     */
    protected $rsvp;

    /**
     * @var array
     */
    protected $url;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var array
     */
    protected $rating;

    /**
     * @var string
     */
    protected $visibility;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var array
     */
    protected $venue;

    /**
     * @var \Raffle\User\UserEntity\UserEntity[]
     */
    protected $users;

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return array
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param array $rsvp
     */
    public function setRsvp($rsvp)
    {
        $this->rsvp = $rsvp;
    }

    /**
     * @return array
     */
    public function getRsvp()
    {
        return $this->rsvp;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param array $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $visibility
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param array $venue
     */
    public function setVenue($venue)
    {
        $this->venue = $venue;
    }

    /**
     * @return array
     */
    public function getVenue()
    {
        return $this->venue;
    }

    /**
     * @param \Raffle\User\UserEntity\UserEntity[] $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @return \Raffle\User\UserEntity\UserEntity[]
     */
    public function getUsers()
    {
        return $this->users;
    }


}
