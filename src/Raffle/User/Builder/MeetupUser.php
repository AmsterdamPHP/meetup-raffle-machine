<?php
namespace Raffle\User\Builder;

use Raffle\User\UserEntity;

class MeetupUser implements BuilderInterface
{
    /**
     * @param array $list
     * @return \Raffle\User\UserEntity[]
     */
    public function buildUserList($list)
    {
        return array_map(array($this, 'buildUser'), $list);
    }

    /**
     * @param mixed $data
     *
     * @return \Raffle\User\UserEntity
     */
    public function buildUser($data)
    {
        $user = new UserEntity();
        $user->setId($data['member']['member_id']);
        $user->setName($data['member']['name']);
        $user->setContactInfo($data['member']['name']);
        $user->setRsvpStatus($data['response']);

        if (isset($data['member_photo'])) {
            $user->setPhoto($data['member_photo']['photo_link']);
            $user->setThumbnail($data['member_photo']['thumb_link']);
        }

        return $user;
    }
}
