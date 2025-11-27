<?php

namespace App\Security;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

readonly class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function __construct(
        #[Autowire('%admin_prefix%')] private string $adminPrefix,
        private \Twig\Environment $twig
    ){
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        if (str_starts_with($request->getRequestUri(), $this->adminPrefix)) {
            return new Response($this->twig->render('bundles/TwigBundle/Exception/error404.html.twig'), Response::HTTP_NOT_FOUND);
        }

        return new Response($this->twig->render('bundles/TwigBundle/Exception/error403.html.twig'), Response::HTTP_FORBIDDEN);
    }
}