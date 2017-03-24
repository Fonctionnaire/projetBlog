<?php

namespace AppBundle\Service\MailForNewEpisode;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;

class MailForNewEpisode extends Controller
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function sendEmailForNewEpisode()
    {
        $users = $this->em->getRepository('AppBundle:User')->findAll();

        foreach ($users as $user)
        {
            $message = \Swift_Message::newInstance()->setSubject('Nouvel épisode')
                ->setFrom('jean@gmail.com')
                ->setTo($user->getEmail())
                ->setBody($this->renderView(
                    'Emails/newEpisodeMail.html.twig'
                ),
                    'text/html'
                );
            $this->get('mailer')->send($message);
        }
    }
}
