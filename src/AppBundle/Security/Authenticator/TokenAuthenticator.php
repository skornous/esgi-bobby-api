<?php

namespace AppBundle\Security\Authenticator;


use AppBundle\Security\JwtConfiguration;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    private $jwtConfiguration;
    private $ssh_public;

    public function __construct(JwtConfiguration $jwtConfiguration, String $ssh_public)
    {
        $this->jwtConfiguration = $jwtConfiguration;
        $this->ssh_public = $ssh_public;
    }

    public function getCredentials(Request $request)
    {
        if ($request->headers->has("Authorization")) {
            list($bearer, $token) = explode(' ', $request->headers->get("Authorization"));

            if ($bearer !== 'Bearer') {
                return false;
            }

            return $token;
        }

        return false;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!$credentials) {
            throw new CustomUserMessageAuthenticationException('Missing Token');
        }

        $token = (new Parser())->parse($credentials);

        if (!$token->verify($this->jwtConfiguration->getSigner(), new Key('file://' . $this->ssh_public))) {
            throw new CustomUserMessageAuthenticationException('Invalid token');
        }

        return $userProvider->loadUserByUsername($token->getClaim('username'));
    }


    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($exception instanceof UnauthorizedHttpException) {
            throw $exception;
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // return token as JsonResponse
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        throw $authException;
    }
}