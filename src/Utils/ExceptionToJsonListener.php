<?php


namespace App\Utils;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

class ExceptionToJsonListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof HttpException) {
            $response = new JsonResponse(
                $exception->getMessage(),
                $exception->getStatusCode(),
                $event->hasResponse() ? $event->getResponse()->headers->all() : []
            );
            $response->setSharedMaxAge(0);
            $event->setResponse($response);
        }
    }
}