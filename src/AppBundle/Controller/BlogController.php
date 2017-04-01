<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Commentaire;
use AppBundle\Entity\Episode;
use AppBundle\Form\Type\CommentaireType;
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
        $episode = $em->getRepository('AppBundle:Episode')->findBy(array(), array('id' => 'desc'),1,0);
        $troisEpisodes = $em->getRepository('AppBundle:Episode')->findBy(array(), array('id' => 'desc'),3,0);
        $lastComment = $em->getRepository('AppBundle:Commentaire')->findBy(array(), array('id' => 'desc'), 1,0);
        return $this->render('index.html.twig', array('episode' => $episode, 'troisEpisodes' => $troisEpisodes, 'lasComment' => $lastComment));
    }

    /**
     * @Route("/episodes", name="episodes")
     * @Method({"GET"})
     */
    public function listeEpisodesAction()
    {
        $em = $this->getDoctrine()->getManager();

        $episodes = $em->getRepository('AppBundle:Episode')->findAll();

        return $this->render('listeEpisode.html.twig', array('episodes' => $episodes));
    }

    /**
     * @Route("/episode/{id}", name="episode")
     * @Method({"GET", "POST"})
     */
    public function episodeAction(Request $request, Episode $episode)
    {
        $newCommentaire = new Commentaire();
        $form = $this->get('form.factory')->create(CommentaireType::class, $newCommentaire);

        $episode = $this->getDoctrine()->getManager()->getRepository('AppBundle:Episode')
            ->getEpisodeWithFirstComments($episode->getId());

        dump($episode);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $newCommentaire->setDate(new \DateTime());
            $newCommentaire->setEpisode($episode);
            $em->persist($newCommentaire);
            $em->flush();

            return $this->redirectToRoute('episode', array('id' => $episode->getId()));
        }
        return $this->render('episode.html.twig', array('form' => $form->createView(),'episode' => $episode));
    }

    /**
     * @Route("/commentaire/{id}", name="reponseComment")
     * @Method({"GET", "POST"})
     */
    public function reponseCommentAction(Request $request,Commentaire $commentaire, Episode $episode)
    {
        $newCommentaire = new Commentaire();
        $form = $this->get('form.factory')->create(CommentaireType::class, $newCommentaire);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $newCommentaire->setDate(new \DateTime());

            $idEpisode = $commentaire->getEpisode();
            $newCommentaire->setEpisode($idEpisode);
            $newCommentaire->setParent($commentaire);

            $em->persist($newCommentaire);
            $em->flush();
            return $this->redirectToRoute('episode', array('id' => $commentaire->getEpisode()->getId()));
        }
        return $this->render('reponseComment.html.twig', array('form' => $form->createView(),'commentaire' => $commentaire));
    }

    /**
     * @Route("/commentaire/{id}/report", name="report")
     * @Method({"GET", "POST"})
     */
    public function reportCommentAction(Commentaire $commentaire)
    {
        $em = $this->getDoctrine()->getManager();
        $commentaire->setReport(true);
        $em->flush();

        return $this->redirectToRoute('episode', array('id' => $commentaire->getEpisode()->getId()));
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
