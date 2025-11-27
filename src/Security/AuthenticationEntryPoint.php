<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

readonly class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function __construct(
        private AccessDeniedHandler $accessDeniedHandler,
        private UrlGeneratorInterface $urlGenerator
    ){
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        $previous = $authException?->getPrevious();

        if (
            $authException instanceof InsufficientAuthenticationException &&
            $previous instanceof AccessDeniedException &&
            $authException->getToken() instanceof RememberMeToken
        ) {
            return $this->accessDeniedHandler->handle(request: $request, accessDeniedException: $previous);
        }

        return new RedirectResponse($this->urlGenerator->generate('login'));
    }
}