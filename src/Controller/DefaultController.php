<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * Page d'accueil
     * 
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return $this->render('default/index.html.twig');
    }

}
