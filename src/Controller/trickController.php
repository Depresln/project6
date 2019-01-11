<?php

namespace App\Controller;

use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Trick;

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
            $trick->setCreatedAt(new \DateTime());

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
    public function show(Trick $trick)
    {
        return $this->render('blog/show.html.twig', [
            'trick' => $trick
        ]);
    }
}
