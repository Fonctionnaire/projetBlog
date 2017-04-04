<?php

namespace AppBundle\Services\EmailsForNewEpisode;

use Doctrine\ORM\EntityManagerInterface;

class EmailsForNewEpisode extends \Twig_Extension
{
    private $twig;
    private $mailer;
    private $em;

    public function __construct(\Swift_Mailer $mailer,\Twig_Environment $twig, EntityManagerInterface $em)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->em = $em;
    }

    public function sendMailForNewEpisode()
    {
        $users = $this->em->getRepository('AppBundle:User')->findAll();
        foreach ($users as $user)
        {
            $message = \Swift_Message::newInstance()->setSubject('Nouvel épisode')
                ->setFrom('jean@gmail.com')
                ->setTo($user->getEmail())
                ->setBody($this->twig->render(
                    'Emails/newEpisodeMail.html.twig', array('user' => $user)
                ),
                    'text/html'
                );
            $this->mailer->send($message);
        }
    }
}
