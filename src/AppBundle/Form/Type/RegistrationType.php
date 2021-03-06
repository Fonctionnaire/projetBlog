<?php
// src/AppBundle/Form/RegistrationType.php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class RegistrationType extends AbstractType
{

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
