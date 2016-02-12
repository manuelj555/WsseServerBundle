<?php

namespace Ku\Bundle\WsseServerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class WebserviceController extends Controller
{
    public function generateNonceAction()
    {
        $nonce = $this->get('wsse_server.security.nonce_generator')->generateNonce();

        return $nonce->getValue();
    }
}
