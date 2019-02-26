<?php

namespace App\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Form\MediaType;
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
     * @Route("/user/{id}", name="user_space")
     */
    public function userSpace(User $user, Request $request, ObjectManager $manager)
    {
        $media = new Media();
        $form = $this->createForm(MediaType::class, $media);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $media->setContent($media)
                  ->setType("1");

            $manager->persist($media);
            $manager->flush();

            return $this->redirectToRoute('blog/userSpace.html.twig', ['id' => $user->getId()]);
        }

        return $this->render('blog/userSpace.html.twig', [
                'user' => $user,
                'mediaForm' => $form->createView()
        ]);
    }
}
