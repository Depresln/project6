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

use App\Entity\User;
use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Form\UserType;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;

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
            'editMode' => $trick->getId() !== null
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
     *
     */
    public function delete(TrickRepository $repo, Request $request, $id)
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
    public function userSpace(User $user, FileUploader $fileUploader, Request $request, ObjectManager $manager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

//        $this->getUser();

        $saveImageName = $user->getAvatarImg();
        $user->setAvatarImg(NULL);

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $file = $user->getAvatarImg();
            $fileName = $fileUploader->upload($file);

            if(empty($fileName)){
                $fileName = $saveImageName;
            }

            $user->setAvatarImg($fileName);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('user_space', ['id' => $user->getId()]);
        }

        $user->setAvatarImg($saveImageName);

        return $this->render('blog/userSpace.html.twig', [
                'user' => $user,
                'avatarForm' => $form->createView()
        ]);
    }
}
