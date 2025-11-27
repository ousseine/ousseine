<?php

namespace App\EventSubscriber;

use App\Controller\Admin\AdminController;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class AdminRequestListener implements EventSubscriberInterface
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
        private string                        $adminPrefix
    ){
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onController',
            KernelEvent::class => 'onKernel'
        ];
    }

    public function onController(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) return;

        $controller = $event->getController();
        if (is_array($controller) && $controller[0] instanceof AdminController && !$this->authorizationChecker->isGranted(User::ROLE_ADMIN)) {
            $this->getException($event);
        }
    }

    public function onKernel(KernelEvent $event): void
    {
        if (!$event->isMainRequest()) return;

        $uri = '/'.trim($event->getRequest()->getRequestUri(), '/').'/';
        $prefix = '/'.trim($this->adminPrefix, '/').'/';
        if (
            substr($uri, 0, mb_strlen($prefix)) === $prefix
            && !$this->authorizationChecker->isGranted(User::ROLE_ADMIN)
        ) $this->getException($event);
    }

    private function getException($event)
    {
        $exception = new AccessDeniedException();
        $exception->setSubject($event->getRequest());
        throw $exception;
    }
}