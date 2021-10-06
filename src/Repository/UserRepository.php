<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findByAuthId(string $authId): ?User
    {
        return $this->findOneBy(['username' => $authId]);
    }
}
