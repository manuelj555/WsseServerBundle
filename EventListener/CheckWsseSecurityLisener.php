<?php

namespace Ku\Bundle\WsseServerBundle\EventListener;

use BeSimple\SoapBundle\Soap\SoapRequest;
use Ku\Bundle\WsseServerBundle\Annotation\CheckWsseSecurity;
use Ku\Bundle\WsseServerBundle\Security\Authenticator\WsseAuthenticator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class CheckWsseSecurityLisener implements EventSubscriberInterface
{
    /**
     * @var WsseAuthenticator
     */
    private $authenticator;

    /**
     * CheckWsseSecurityLisener constructor.
     * @param WsseAuthenticator $authenticator
     */
    public function __construct(WsseAuthenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        /** @var SoapRequest $request */
        $request = $event->getRequest();
        /** @var CheckWsseSecurity $configuration */
        if (!$configuration = $request->attributes->get('_check_wsse_security')) {
            return;
        }

        $application = $this->authenticator->authenticate($request);

        $request->attributes->remove('_check_wsse_security');
        $request->attributes->set('_application', $application);
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::CONTROLLER => 'onKernelController'];
    }
}