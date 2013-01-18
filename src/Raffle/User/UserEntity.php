<?php
namespace Raffle\User;

class UserEntity
{

    const ORIGIN_MEETUP = 'meetup';
    const ORIGIN_REDIS  = 'redis';

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $contactInfo;

    /**
     * @var string
     */
    public $thumbnail;

    /**
     * @var string
     */
    public $photo;

    /**
     * @var string
     */
    public $rsvpStatus;

    /**
     * @param string $contactInfo
     */
    public function setContactInfo($contactInfo)
    {
        $this->contactInfo = $contactInfo;
    }

    /**
     * @return string
     */
    public function getContactInfo()
    {
        return $this->contactInfo;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
     * @param string $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    /**
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param string $rsvpStatus
     */
    public function setRsvpStatus($rsvpStatus)
    {
        $this->rsvpStatus = $rsvpStatus;
    }

    /**
     * @return string
     */
    public function getRsvpStatus()
    {
        return $this->rsvpStatus;
    }

    /**
     * @param string $thumbnail
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @return string
     */
    public function getOrigin()
    {
        return (strpos($this->getId(), 'user') !== false)? self::ORIGIN_REDIS : self::ORIGIN_MEETUP;
    }

    /**
     * Returns a json representation of the user
     */
    public function getJson()
    {
       return json_encode($this);
    }

}
