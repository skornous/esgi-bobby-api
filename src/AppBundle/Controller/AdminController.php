<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Admin\LoginType;
use AppBundle\Security\JwtConfiguration;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminController
 * @package AppBundle\Controller
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/login", name="adminLoginPage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        $login = new User();
        $form = $this->createForm(LoginType::class, $login);

        $form->handleRequest($request);
//dump($request);die;
        if ($form->isSubmitted() && $form->isValid()) {
//            dump($form->getData());die;

            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(["email" => $login->getEmail()]);
            dump($user);die;
            if ($user) {
                dump($user);die;
                $jwtHandler = $this->get('yearlyapi.security.jwtconfiguration');
                $jwtHandler->getBuilder()
                    ->setIssuer('%baseurl%')
                    ->setIssuedAt(time())
                    ->setExpiration(time() + JwtConfiguration::$TTL)
                    ->set("username", $login->getUsername())
                ;

                $token = $jwtHandler->sign()->getToken();

                dump($token);die;

                return $this->redirectToRoute('login_success');
            }
        }

        return $this->render('Admin/login.html.twig', array(
            'loginForm' => $form->createView(),
        ));
    }

}