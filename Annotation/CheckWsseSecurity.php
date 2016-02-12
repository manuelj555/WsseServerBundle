<?php

namespace Ku\Bundle\WsseServerBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 *
 * @Annotation
 */
class CheckWsseSecurity extends ConfigurationAnnotation
{

    public function getAliasName()
    {
        return 'check_wsse_security';
    }

    public function allowArray()
    {
        return false;
    }
}