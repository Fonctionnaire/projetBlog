<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BlogController extends Controller
{

    /**
     * @Route("/", name="homepage")
     * @Method({"GET"})
     */
    public function indexAction()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/admin", name="admin")
     * @Method({"GET", "POST"})
     */
    public function adminAction()
    {
        return $this->render('admin.html.twig');
    }

    /**
     * @Route("/episodes", name="episodes")
     * @Method({"GET", "POST"})
     */
    public function episodeAction()
    {
        return $this->render('episodes.html.twig');
    }

    /**
     * @Route("/a-propos", name="apropos")
     * @Method({"GET"})
     */
    public function proposAction()
    {
        return $this->render('apropos.html.twig');
    }
}