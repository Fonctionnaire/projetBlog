<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Commentaire;
use AppBundle\Entity\Episode;
use AppBundle\Form\Type\EpisodeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     * @Method({"GET"})
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

            $em->persist($newEpisode);
            $em->flush();

            $this->get('app.emails_for_new_episode')->sendMailForNewEpisode();

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


        $form = $this->get('form.factory')->create(EpisodeType::class, $episode);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('admin');
        }
        return $this->render('adminUpdate.html.twig', array('form' => $form->createView(), 'episode' => $episode));
    }

    /**
     * @Route("/admin/delete/{id}", name="deleteBillet")
     * @Method({"POST"})
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
     * @Method({"POST"})
     */
    public function moderationCommentsAction(Commentaire $commentaire)
    {
        $em = $this->getDoctrine()->getManager();
        $commentaire->getId();
        $commentaire->setTexte('Ce commentaire a été supprimé par le modérateur');
        $commentaire->setReport(false);
        $em->flush();

        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/admin/dontremove/commentaire/{id}", name="moderationDontDeleteComment")
     * @Method({"POST"})
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
