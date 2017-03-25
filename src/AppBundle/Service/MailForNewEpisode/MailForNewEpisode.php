<?php

namespace AppBundle\Service\MailForNewEpisode;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Container;

class MailForNewEpisode extends Controller
{
    protected $em;
    protected $container;

    public function __construct(EntityManagerInterface $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
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
