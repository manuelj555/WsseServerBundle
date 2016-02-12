<?php

namespace Ku\Bundle\WsseServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Nonce
 *
 * @ORM\Table(name="ku_wsse_nonce")
 * @ORM\Entity(repositoryClass="Ku\Bundle\WsseServerBundle\Repository\NonceRepository")
 */
class Nonce
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, unique=true)
     */
    private $value;

    /**
     * @var bool
     *
     * @ORM\Column(name="used", type="boolean", nullable=true)
     */
    private $used = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * Nonce constructor.
     * @param string $value
     * @param bool $createdAt
     */
    public function __construct($value, $createdAt)
    {
        $this->value = $value;
        $this->createdAt = $createdAt;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Nonce
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set used
     *
     * @param boolean $used
     *
     * @return Nonce
     */
    public function setUsed($used)
    {
        $this->used = $used;

        return $this;
    }

    /**
     * Get used
     *
     * @return bool
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * @return boolean
     */
    public function isCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param boolean $createdAt
     * @return Nonce
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}

