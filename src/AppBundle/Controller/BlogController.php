<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Commentaire;
use AppBundle\Entity\Connexion;
use AppBundle\Entity\Episode;
use AppBundle\Form\Type\ConnexionType;
use AppBundle\Form\Type\EpisodeType;
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

        return $this->render('index.html.twig', array('episode' => $episode));
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

    /**
     * @Route("/admin", name="admin")
     * @Method({"GET", "POST"})
     */
    public function adminAction()
    {
        $em = $this->getDoctrine()->getManager();
        $episodes = $em->getRepository('AppBundle:Episode')->findAll();


        return $this->render('admin.html.twig', array('episodes' => $episodes));
    }

    /**
     * @Route("/connexion", name="connexion")
     * @Method({"GET","POST"})
     */
    public function connexionAction(Request $request)
    {
        $connexion = new Connexion();
        $form = $this->get('form.factory')->create(ConnexionType::class, $connexion);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $identifiants = $em->getRepository('AppBundle:Connexion')->find(1);

            $log = $identifiants->getLogin();
            $mdp = $identifiants->getMdp();

            $logSubmit = $form->get('login')->getData();
            $mdpSubmit = $form->get('mdp')->getData();
            if($log != $logSubmit || $mdp != $mdpSubmit)
            {
                $this->addFlash('error', 'Mauvais login ou mot de passe');
            }else{
                return $this->redirectToRoute('admin');
            }
        }

        return $this->render('connexion.html.twig', array('form' => $form->createView()));
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
        return $this->render('adminComments.html.twig');
    }

}
