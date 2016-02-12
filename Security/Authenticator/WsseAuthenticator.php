<?php

namespace Ku\Bundle\WsseServerBundle\Security\Authenticator;

use BeSimple\SoapBundle\Soap\SoapHeader;
use BeSimple\SoapBundle\Soap\SoapRequest;
use BeSimple\SoapBundle\Util\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Ku\Bundle\WsseServerBundle\Entity\Nonce;

/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class WsseAuthenticator
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var array
     */
    private $applications = [];

    /**
     * WsseAuthenticator constructor.
     * @param ObjectManager $om
     * @param array $applications
     */
    public function __construct(ObjectManager $om, array $applications)
    {
        $this->om = $om;
        $this->applications = $applications;
    }

    public function authenticate(SoapRequest $request)
    {
        $this->verifySoapHeaders($headers = $request->getSoapHeaders());
        $nonceValue = $headers->get('Nonce');

        if (!($nonce = $this->getNonce($nonceValue)) || $nonce->getUsed()) {
            throw new \SoapFault('INVALID_NONCE', 'The nonce sent is invalid or has been used already');
        }

        $this->markUsedNonce($nonce);

        $application = $this->getApplication($headers->get('Username'));

        $this->verifyCredentials($nonce, $headers, $application);

        return $application;
    }

    /**
     * Chequeamos que hayan enviado los headers necesarios
     * @param Collection|SoapHeader[] $soapHeaders
     * @throws \SoapFault
     */
    protected function verifySoapHeaders(Collection $soapHeaders)
    {
        if (!$soapHeaders->has('Username') || !$soapHeaders->has('PasswordDigest')
            || !$soapHeaders->has('Nonce') || !$soapHeaders->has('Created')
        ) {
            throw new \SoapFault(
                'INVALID_OR_INCOMPLETE_HEADERS',
                'The soap headers sent with the request are invalid or incomplete'
            );
        }
    }

    /**
     * @param $nonceValue
     * @return null|Nonce
     */
    protected function getNonce($nonceValue)
    {
        return $this->om->getRepository('WsseServerBundle:Nonce')->findOneByValue($nonceValue);
    }

    /**
     * @param $nonce
     */
    protected function markUsedNonce($nonce)
    {
        $nonce->setUsed(true);
        $this->om->flush();
    }

    /**
     * @param $username
     * @return bool
     */
    protected function applicationExists($username)
    {
        return isset($this->applications[(string)$username]);
    }

    /**
     * @param $username
     * @return array
     * @throws \SoapFault
     */
    protected function getApplication($username)
    {
        if(!$this->applicationExists($username)){
            throw new \SoapFault('INVALID_USERNAME', 'The Username cannot be recognized');
        }

        return $this->applications[(string)$username];
    }

    /**
     * @param $nonce
     * @param $headers
     * @param $application
     * @throws \SoapFault
     */
    protected function verifyCredentials($nonce, $headers, $application)
    {
        $hash = base64_encode(sha1($nonce->getValue().$headers->get('Created').$application['password']));

        if ($headers->get('PasswordDigest') != $hash) {
            throw new \SoapFault('INVALID_PASSWORD', 'The application cannot be recognized');
        }
    }
}