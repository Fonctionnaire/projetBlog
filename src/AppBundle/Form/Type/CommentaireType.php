<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentaireType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('auteur', TextType::class, array(
                'label' => false,
                'attr' => ['placeholder' => 'Votre nom ou pseudo'],
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 2))
            )))
            ->add('texte', TextareaType::class, array(
                'label' => false,
                'attr' => ['placeholder' => 'Ecrivez ici votre commentaire'],
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 2))
                )))
            ->add('envoyer', SubmitType::class)
            ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Commentaire'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_commentaire';
    }


}
