<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Trick;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(TrickRepository $repo)
    {
        $tricks = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'tricks'  => $tricks
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function trick_show(Trick $trick)
    {
        return $this->render('blog/show.html.twig', [
            'trick' => $trick
        ]);
    }
}
