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

    public function sendMail($mail, \Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('admin@snowtricks.com')
            ->setTo($mail)
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'emails/registration.html.twig',
                    ['name' => $name]
                ),
                'text/html'
            )
        ;

        $mailer->send($message);

        return $this->redirectToRoute('security_login');
    }

    /**
     * @Route("/reset", name="reset_password")
     */
    public function resetPassword(Request $request, ObjectManager $manager)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $mail = $user->getEmail();
            $this->sendMail($mail, $mailer);

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/reset.html.twig');
    }

//$to_email = $user->getEmail;
//$subject = 'Testing PHP Mail';
//$message = 'This mail is sent using the PHP mail function';
//$headers = 'From: noreply @ company . com';
//mail($to_email,$subject,$message,$headers);
}
