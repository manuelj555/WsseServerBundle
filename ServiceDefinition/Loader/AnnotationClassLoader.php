<?php

namespace Ku\Bundle\WsseServerBundle\ServiceDefinition\Loader;

use BeSimple\SoapBundle\ServiceDefinition\Definition;
use BeSimple\SoapBundle\ServiceDefinition\Loader\AnnotationClassLoader as BaseClassLoader;
use BeSimple\SoapBundle\ServiceDefinition\Method;
use Doctrine\Common\Annotations\Reader;
use Ku\Bundle\WsseServerBundle\Annotation\CheckWsseSecurity;
use Symfony\Component\Config\Loader\LoaderResolverInterface;

/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class AnnotationClassLoader extends BaseClassLoader
{
    /**
     * @var BaseClassLoader
     */
    private $classLoader;

    /**
     * AnnotationClassLoader constructor.
     * @param BaseClassLoader $classLoader
     */
    public function __construct(BaseClassLoader $classLoader, Reader $reader)
    {
        $this->classLoader = $classLoader;
        $this->reader = $reader;
    }


    public function load($class, $type = null)
    {
        /** @var Definition $serviceDefinition */
        $serviceDefinition = $this->classLoader->load($class, $type);
        $class = new \ReflectionClass($class);

        $setHeadersToAllMethods = false;
        $hasSecuredMethods = false;
        foreach ($this->reader->getClassAnnotations($class) as $annotation) {
            if ($annotation instanceof CheckWsseSecurity) {
                $setHeadersToAllMethods = true;
                $hasSecuredMethods = true;
                break;
            }
        }

        /** @var Method $method */
        foreach ($serviceDefinition->getMethods() as $method) {
            if ($setHeadersToAllMethods) {
                $this->addWsseHeaders($method);
            } else {
                $reflectionMethod = new \ReflectionMethod($method->getController());

                foreach ($this->reader->getMethodAnnotations($reflectionMethod) as $annotation) {
                    if ($annotation instanceof CheckWsseSecurity) {
                        $hasSecuredMethods = true;
                        $this->addWsseHeaders($method);
                    }
                }
            }
        }

        if ($hasSecuredMethods) {
            $this->addGenerateNonceMethod($serviceDefinition);
        }

        return $serviceDefinition;
    }

    public function supports($resource, $type = null)
    {
        return $this->classLoader->supports($resource, $type);
    }

    public function getResolver()
    {
        return $this->classLoader->getResolver();
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
        $this->classLoader->setResolver($resolver);
    }

    public function import($resource, $type = null)
    {
        return $this->classLoader->import($resource, $type);
    }

    public function resolve($resource, $type = null)
    {
        return $this->classLoader->resolve($resource, $type);
    }

    /**
     * @param Method $method
     */
    protected function addWsseHeaders(Method $method)
    {
        $method->addHeader('Username', 'string');
        $method->addHeader('PasswordDigest', 'string');
        $method->addHeader('Created', 'string');
        $method->addHeader('Nonce', 'string');
    }

    /**
     * @param Definition $serviceDefinition
     * @throws \Exception
     */
    protected function addGenerateNonceMethod(Definition $serviceDefinition)
    {
        $serviceDefinition->addMethod($method = new Method(
            'generateNonce',
            'Ku\Bundle\WsseServerBundle\Controller\WebserviceController::generateNonceAction')
        );

        $method->setOutput('string');
    }

}