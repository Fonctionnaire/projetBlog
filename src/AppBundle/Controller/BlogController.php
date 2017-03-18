<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Commentaire;
use AppBundle\Entity\Episode;
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
        $em = $this->getDoctrine()->getManager();
        $episode = $em->getRepository('AppBundle:Episode')->find(1);

        return $this->render('index.html.twig', array('episode' => $episode));
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
     * @Route("/connexion", name="connexion")
     * @Method({"POST"})
     */
    public function connexionAction()
    {
        return $this->render('connexion.html.twig');
    }

    /**
     * @Route("/episodes", name="episodes")
     * @Method({"GET"})
     */
    public function listeEpisodesAction()
    {
        return $this->render('listeEpisode.html.twig');
    }

    /**
     * @Route("/episode/{id}", name="episode")
     * @Method({"GET", "POST"})
     */
    public function episodeAction(Episode $episode, Commentaire $commentaire)
    {


        return $this->render('episode.html.twig', array('episode' => $episode, 'commentaire' => $commentaire));
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
