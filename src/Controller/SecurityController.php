<?php

namespace App\Controller;

use App\Entity\ForgotPasswordToken;
use App\Entity\User;
use App\Form\ForgotPasswordStep1Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Connexion et déconnexion. Généré par Symfony presque à 100%.
 * L'inscription du user se fait dans Controller/Admin/UserController.php
 *
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/mot-de-passe-oublie", name="security_forgot_password_step_1")
     */
    public function forgotPasswordStep1(Request $request)
    {
        $userRepo = $this->getDoctrine()->getRepository(User::class);

        $form = $this->createForm(ForgotPasswordStep1Type::class, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $email = $form->getData()['email'];

            $foundUser = $userRepo->findOneBy(["email" => $email]);
            if (empty($foundUser)){
                $this->addFlash("danger", "Oups ! Cet email n'a pas été trouvé !");
                $this->redirectToRoute('security_forgot_password_step_1');
            }

            $token = new ForgotPasswordToken();

            $em = $this->getDoctrine()->getManager();
            $em->persist($token);
            $em->flush();

            //@TODO: envoyer le mail ici avec le lien

            return $this->redirectToRoute('security_forgot_password_step_2');
        }

        return $this->render('security/forgot_step_1.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mot-de-passe-oublie/message-envoye", name="security_forgot_password_step_2")
     */
    public function forgotPasswordStep2()
    {
        return $this->render('security/forgot_step_2.html.twig');
    }


    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
