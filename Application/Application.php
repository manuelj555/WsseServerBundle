<?php

namespace Ku\Bundle\WsseServerBundle\Application;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class Application implements ApplicationInterface
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;
    /**
     * @var array
     */
    private $parameters;

    /**
     * Application constructor.
     * @param string $name
     * @param string $username
     * @param string $password
     * @param array $parameters
     */
    public function __construct($name, $username, $password, array $parameters = array())
    {
        $this->name = $name;
        $this->username = $username;
        $this->password = $password;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    public function hasParameter($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    public function getParameter($name, $default = null)
    {
        return $this->hasParameter($name) ? $this->parameters[$name] : $default;
    }
}