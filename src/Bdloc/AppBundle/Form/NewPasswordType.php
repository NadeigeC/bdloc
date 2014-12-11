<?php

namespace Bdloc\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewPasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'Les mots de passe doivent correspondre',
                    'options' => array(
                    'required' => true),

                    'first_options'  => array(
                    'label' => 'Nouveau mot de passe',
                    'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Nouveau mot de passe')),


                    'second_options' => array(
                    'label' => 'Confirmez votre nouveau mot de passe',
                    'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Confirmez votre nouveau mot de passe')),
                    ))

            ->add('submit','submit', array(
                    "label" =>"Enregistrer",
                    'attr' => array(
                    'class' => 'btn red-button btn-default-red')
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bdloc\AppBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bdloc_appbundle_user';
    }
}
