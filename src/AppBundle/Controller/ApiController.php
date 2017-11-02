<?php

namespace AppBundle\Controller;

use AppBundle\Security\JwtConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api")
 */
class ApiController extends Controller
{
    /**
     * @Route("/", name="apiPage")
     */
    public function apiAction()
    {
        return new JsonResponse("ok");
    }

    /**
     * @Route("/token", name="getToken")
     * @param Request $request
     * @return Response
     */
    public function getApiTokenAction(Request $request): Response
    {
        $login = $request->get('login');
        $password = $request->get('password');
        if ($login && $password) {
            $jwtHandler = $this->get('yearlyapi.security.jwtconfiguration');
            $jwtHandler->sign()
                ->setIssuer('%baseurl%')
                ->setIssuedAt(time())
                ->setExpiration(time() + JwtConfiguration::$TTL)
                ->set("username", $login)
            ;
            return new JsonResponse($jwtHandler->getToken());
        }

        return $this->redirect("/");
    }
}