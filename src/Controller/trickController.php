<?php

namespace App\Controller;

use App\Service\FileUploader;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\User;
use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Form\PassType;
use App\Form\UserType;
use App\Form\MediaType;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use App\Repository\MediaRepository;

class trickController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(TrickRepository $repo)
    {
        $tricks = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'trickController',
            'tricks'  => $tricks
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function trickForm(Trick $trick = null, Request $request, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if(!$trick){
            $trick = new Trick();
        }

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $trick->setCreatedAt(new \DateTime())
                  ->setUser($this->getUser());

            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $trick->getId()]);
        }

        return $this->render('blog/create.html.twig', [
            'formTrick' => $form->createView(),
            'editMode' => $trick->getId() !== null,
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Trick $trick, Request $request, ObjectManager $manager)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setTrick($trick)
                    ->setUser($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $trick->getId()]);
        }
        return $this->render('blog/show.html.twig', [
            'trick' => $trick,
            'commentForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/blog/delete/{id}", name="blog_delete")
     */
    public function delete(TrickRepository $repo, $id)
    {
        $trick = $repo->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($trick);
        $entityManager->flush();

        $response = new Response();
        $response->send();

        return $this->index($repo);
    }

    /**
     * @Route("/user/{id}", name="user_space")
     */
    public function userSpace(User $user, FileUploader $fileUploader, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $check = $this->getUser();

        if($user == $check){
            $saveImageName = $user->getAvatarImg();
            $savePassword = $user->getPassword();
            $user->setAvatarImg(NULL);

            $form = $this->createForm(UserType::class, $user);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){

                $file = $user->getAvatarImg();

                if(empty($file)){
                    $fileName = $saveImageName;
                }else{
                    $fileName = $fileUploader->upload($file);
                    unlink($saveImageName); // care defaultavatar
                }

                $newPassword = $user->getPassword();

                if(empty($newPassword)){
                    $password = $savePassword;
                }else{
                    $password = $encoder->encodePassword($user, $newPassword);
                }

                $user->setAvatarImg($fileName);
                $user->setPassword($password);

                $manager->persist($user);
                $manager->flush();

                return $this->redirectToRoute('user_space', ['id' => $user->getId()]);
            }

            $user->setAvatarImg($saveImageName);

            return $this->render('blog/userSpace.html.twig', [
                    'user' => $user,
                    'avatarForm' => $form->createView()
            ]);
        }else{
            return $this->render('blog/userSpace.html.twig', [
                'user' => $user
            ]);
        }
    }

    /**
     * @Route("/addmedia/{id}", name="add_media")
     */
    public function addMedia(Trick $trick, FileUploader $fileUploader, Request $request, ObjectManager $manager)
    {
        $media = new Media();

        $form = $this->createForm(MediaType::class, $media);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $file = $media->getContent();

            $fileName = $fileUploader->upload($file);

            $media->setContent($fileName);
            $media->setTrick($trick);
            $media->setType(1);

            $manager->persist($media);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $trick->getId()]);
        }

        return $this->render('blog/addMedia.html.twig', [
            'media' => $media,
            'mediaForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/media/delete/{id}", name="media_delete")
     */
    public function deleteMedia(MediaRepository $repo, $id)
    {
        $media = $repo->find($id);
        $trick = $media->getTrick();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($media);
        $entityManager->flush();

        $response = new Response();
        $response->send();

        return $this->redirectToRoute('blog_show', ['id' => $trick->getId()]);
    }
}
