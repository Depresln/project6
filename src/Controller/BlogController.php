<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Trick;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(Trick::class);

        $tricks = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'tricks'  => $tricks
        ]);
    }

    /**
     * @Route("/blog/12", name="blog_show")
     */
    public function trick_show()
    {
        return $this->render('blog/show.html.twig');
    }
}
