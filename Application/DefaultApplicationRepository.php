<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ku\Bundle\WsseServerBundle\Application;

/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class DefaultApplicationRepository implements ApplicationRepositoryInterface
{
    /**
     * @var array
     */
    private $applications;

    /**
     * DefaultApplicationRepository constructor.
     * @param array $applications
     */
    public function __construct(array $applications)
    {
        $this->applications = $applications;
    }

    public function findByUsername($username)
    {
        $username = (string)$username;

        if (!isset($this->applications[$username])) {
            throw new \InvalidArgumentException(sprintf('Application with username "%s" not found', $username));
        }

        $data = $this->applications[$username];

        return new Application($data['name'], $data['username'], $data['password'], $data['parameters']);
    }
}