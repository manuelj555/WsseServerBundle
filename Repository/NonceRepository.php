<?php

namespace Ku\Bundle\WsseServerBundle\Repository;

/**
 * NonceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NonceRepository extends \Doctrine\ORM\EntityRepository
{
    public function findOneByValue($value)
    {
        return $this->findOneBy(['value' => $value]);
    }
}
