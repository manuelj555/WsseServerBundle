<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ku\Bundle\WsseServerBundle\Security;

use Doctrine\Common\Persistence\ObjectManager;
use Ku\Bundle\WsseServerBundle\Entity\Nonce;
use Ku\Bundle\WsseServerBundle\Repository\NonceRepository;

/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class NonceGenerator
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * NonceGenerator constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @return Nonce
     */
    public function generateNonce()
    {
        do {
            $value = $this->generateNonceValue();
            $nonce = $this->getNonceByValue($value);
        } while (!is_null($nonce));

        $nonce = new Nonce($value, new \DateTime('now'));

        $this->om->persist($nonce);
        $this->om->flush();

        return $nonce;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function getNonceByValue($value)
    {
        return $this->om->getRepository('WsseServerBundle:Nonce')->findOneByValue($value);
    }

    private function generateNonceValue()
    {
        return md5(openssl_random_pseudo_bytes(32).date('Ymdhis'));
    }
}