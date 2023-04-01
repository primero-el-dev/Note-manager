<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Bundle\SecurityBundle\Security;

/**
 * This class acts as middleware
 */
class ApiRequestListener implements EventSubscriberInterface
{
    public function __construct(private Security $security) {}

    public function wrapApiResponse(ResponseEvent $event)
    {
        if (str_starts_with($event->getRequest()->getRequestUri(), '/api')) {
        	$response = $event->getResponse();
        	$data = [
        		'success' => $response->getStatusCode() === 200,
        		'data' => json_decode($response->getContent(), true),
        	];
        	if ($this->security->getUser()) {
        		$data['sessionExpiry'] = time() + 1200;
        	}
        	$response->setContent(json_encode($data));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => [['wrapApiResponse']],
        ];
    }
}