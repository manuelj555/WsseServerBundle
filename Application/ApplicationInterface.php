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
interface ApplicationInterface
{
    public function getName();
    public function getUsername();
    public function getPassword();
}