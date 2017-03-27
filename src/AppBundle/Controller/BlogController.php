<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Commentaire;
use AppBundle\Entity\Episode;
use AppBundle\Form\Type\CommentaireType;
use AppBundle\Form\Type\EpisodeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\UserBundle\Model\UserManagerInterface;


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

        $comments = $this->getDoctrine()->getManager()->getRepository('AppBundle:Episode')->getCommentWithResponses();

dump($comments);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $newCommentaire->setDate(new \DateTime());
            $newCommentaire->setEpisode($episode);
            $em->persist($newCommentaire);
            $em->flush();

            return $this->redirectToRoute('episode', array('id' => $episode->getId()));
        }

        return $this->render('episode.html.twig', array('form' => $form->createView(),'episode' => $episode, 'comments' => $comments));
    }

    /**
     * @Route("/commentaire/{id}", name="reponseComment")
     * @Method({"GET", "POST"})
     */
    public function reponseCommentAction(Request $request,Commentaire $commentaire)
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
            return $this->redirectToRoute('homepage');
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

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/a-propos", name="apropos")
     * @Method({"GET"})
     */
    public function proposAction()
    {
        return $this->render('apropos.html.twig');
    }

    /**
     * @Route("/admin", name="admin")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function adminAction()
    {
        $em = $this->getDoctrine()->getManager();
        $episodes = $em->getRepository('AppBundle:Episode')->findAll();
        $commentaires = $em->getRepository('AppBundle:Commentaire')->findAll();

        return $this->render('admin.html.twig', array('episodes' => $episodes, 'commentaires' => $commentaires));
    }

    /**
     * @Route("/admin/nouveau-billet", name="nouveauBillet")
     * @Method({"GET", "POST"})
     */
    public function adminAddBilletAction(Request $request)
    {
        $newEpisode = new Episode();
        $form = $this->get('form.factory')->create(EpisodeType::class, $newEpisode);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $newEpisode->setDate(new \DateTime());
            $em->persist($newEpisode);
            $em->flush();

            $this->get("app.send_email_for_new_episode")->sendEmailForNewEpisode();

            return $this->redirectToRoute('admin');
        }

        return $this->render('adminAddBillet.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/admin/update/{id}", name="updateBillet")
     * @Method({"GET", "POST"})
     */
    public function adminUpdateBilletAction(Request $request, Episode $episode)
    {

        $newEpisode = $episode;
        $form = $this->get('form.factory')->create(EpisodeType::class, $newEpisode);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('adminUpdate.html.twig', array('form' => $form->createView(),'id' => $episode->getId(), 'episode' => $newEpisode));
    }

    /**
     * @Route("/admin/delete/{id}", name="deleteBillet")
     * @Method({"GET", "POST"})
     */
    public function adminDeleteBilletAction(Episode $episode)
    {
            $em = $this->getDoctrine()->getManager();
            $em->remove($episode);
            $em->flush();
            $this->addFlash('notice', "L'épisode a bien été supprimé.");
            return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/admin/commentaire/{id}", name="moderationComment")
     * @Method({"GET", "POST"})
     */
    public function moderationCommentsAction(Commentaire $commentaire)
    {
        $em = $this->getDoctrine()->getManager();
        $commentaire->getId();
        $em->remove($commentaire);
        $em->flush();

        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/admin/dontremove/commentaire/{id}", name="moderationDontDeleteComment")
     * @Method({"GET", "POST"})
     */
    public function moderationDontDeleteCommentAction(Commentaire $commentaire)
    {

        $em = $this->getDoctrine()->getManager();
        $commentaire->getId();
        $commentaire->setReport(false);
        $em->flush();

        return $this->redirectToRoute('admin');

    }

}
