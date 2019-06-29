<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="security_registration")
     */
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $user->setCreatedAt(new \DateTime());
            $user->setIsAdmin(0);
            $user->setActive(0);
            $user->setAvatarImg("defaultAvatar.jpg");

            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="security_login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout() {}

    /**
     * @Route("/reset", name="reset_password")
     */
    public function resetPassword(Request $request, ObjectManager $manager)
    {
        $mail = $request->get("_username");

        if(!empty($mail)){
            $to_email = $mail;
            $token = md5(uniqid(rand(), true));

            $subject = 'Reset password - Snowtricks';
            $message = 'Someone asked to reset your password. If it\'s you, click on the following link to continue the reset process: www.project6.nicolasdep.com/resetPW?token=' . $token;
            $headers = 'From: noreply@snowtricks.com';
            mail($to_email,$subject,$message,$headers);

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/reset.html.twig');
    }

    /**
     * @Route("/resetPW", name="reset_confirm")
     */
    public function resetPW()
    {
        echo "Function not available yet, please come back later";
    }
}
