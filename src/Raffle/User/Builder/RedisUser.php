<?php
namespace Raffle\User\Builder;

use Raffle\User\UserEntity;

class RedisUser implements BuilderInterface
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
     * @return \Raffle\User\UserEntity
     */
    public function buildUser($data)
    {
        $data = json_decode($data);

        if (isset($data->member)) {
            return $this->buildFromLegacyRedisData($data);
        }

        $user  = new UserEntity();
        $user->setId($data->id);
        $user->setName($data->name);
        $user->setEmail($data->email);
        $user->setPhoto($data->photo);
        $user->setThumbnail($data->thumbnail);

        return $user;
    }

    protected function buildFromLegacyRedisData($data)
    {
        $user  = new UserEntity();
        $user->setId($data->member->member_id);
        $user->setName($data->member->name);
        $user->setPhoto($data->member_photo->highres_link);
        $user->setThumbnail($data->member_photo->thumb_link);

        return $user;
    }

}
