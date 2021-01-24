<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Firebase\JWT\JWT;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class JwtAuthenticator extends AbstractGuardAuthenticator implements AuthenticationEntryPointInterface
{

    private $params;
    private $userRepository;

    public function __construct(ContainerBagInterface $params, UserRepository $repository)
    {
        $this->params = $params;
        $this->userRepository = $repository;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        // todo
        $data = ["message" => 'Authentication Required!'];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supports(Request $request)
    {
        // todo
        return $request->headers->has("Authorization");
    }

    public function getCredentials(Request $request)
    {
        // todo
        return $request->headers->get('Authorization');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // todo
        try {
            $credentials = str_replace('Bearer ', '', $credentials);
            $jwt = (array) JWT::decode($credentials, $this->params->get('jwt_secret'), ['HS256']);

            return $this->userRepository->findOneBy(['email' => $jwt['user']]);
        } catch (\Exception $exception) {
            throw new AuthenticationException($exception->getMessage());
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // todo
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // todo
        return new JsonResponse([
            'message' => $exception->getMessage()
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        // todo
        return;
    }

    public function supportsRememberMe()
    {
        // todo
        return false;
    }
}
