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
interface ApplicationRepositoryInterface
{
    /**
     * @param $username
     * @return ApplicationInterface
     *
     * @throws \InvalidArgumentException si no encuentra la aplicación por username
     */
    public function findByUsername($username);
}