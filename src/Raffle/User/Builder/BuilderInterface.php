<?php
namespace Raffle\User\Builder;

interface BuilderInterface
{

    /**
     * @param array $list
     */
    public function buildUserList($list);

    /**
     * @param mixed $data
     */
    public function buildUser($data);

}
