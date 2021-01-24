<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Firebase\JWT\JWT;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("auth/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {


        $password = $request->get('password');
        $email = $request->get('email');

        $this->repository->registerUser($encoder, $email, $password);

        return new JsonResponse(['status' => 'User created!'], Response::HTTP_OK);
    }

    /**
     * @Route("auth/login", name="login", methods={"GET"})
     */
    public function login(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $email = $request->get('email');
        $password = $request->get('password');
        $user = $this->repository->findOneBy(['email' => $email]);
        if (!$user || !$encoder->isPasswordValid($user, $password)) {
            return $this->json([
                'message' => 'email or password is wrong.',
            ]);
        }

        $payload = [
            'user' => $user->getUsername(),
            "exp" => (new \DateTime())->modify("+10 seconds")->getTimestamp()
        ];

        $jwt = JWT::encode($payload, $this->getParameter('jwt_secret'), "HS256");

        return $this->json([
            'message' => 'success!',
            'token' => sprintf('Bearer %s', $jwt),
        ]);
    }
}
