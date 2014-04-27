<?php

namespace QafooLabs\Bundle\FrameworkExtraBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Psr\Log\LoggerInterface;

use Exception;
use ReflectionClass;

/**
 * Converts Exceptions into Symfony HttpKernel Exceptions before rendering the exception page.
 */
class ConvertExceptionListener
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array<string, string>
     */
    private $exceptionClassMap;

    public function __construct(LoggerInterface $logger = null, array $exceptionClassMap = array())
    {
        $this->logger = $logger;
        $this->exceptionClassMap = $exceptionClassMap;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof HttpExceptionInterface) {
            return;
        }

        $convertedExceptionClass = $this->findConvertToExceptionClass($exception);

        if (!$convertedExceptionClass) {
            return;
        }

        $this->logException($exception);

        $convertedException = $this->convertException($exception, $convertedExceptionClass);
        $event->setException($convertedException);
    }

    private function convertException(Exception $exception, $convertToExceptionClass)
    {
        $reflectionClass = new ReflectionClass($convertToExceptionClass);
        $constructor = $reflectionClass->getConstructor();
        $args = array();

        foreach ($constructor->getParameters() as $parameter) {
            if ($parameter->getName() === 'previous') {
                $args[] = $exception;
            } else if ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();
            } else {
                return;
            }
        }

        return $reflectionClass->newInstanceArgs($args);
    }

    private function findConvertToExceptionClass(Exception $exception)
    {
        $exceptionClass = get_class($exception);

        foreach ($this->exceptionClassMap as $originalExceptionClass => $convertedExceptionClass) {
            if ($exceptionClass === $originalExceptionClass || is_subclass_of($exceptionClass, $originalExceptionClass)) {
                return $convertedExceptionClass;
            }
        }

        return null;
    }

    private function logException(Exception $exception)
    {
        if ($this->logger === null) {
            return;
        }

        $message = sprintf(
            'Uncaught PHP Exception %s: "%s" at %s line %s',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        $this->logger->critical($message, array('exception' => $exception));
    }
}
