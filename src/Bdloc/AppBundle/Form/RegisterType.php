<?php

namespace Bdloc\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegisterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('username', 'text', array(
                    'label'=> "Pseudo",
                    'attr' => array(
                    'placeholder' => 'Votre pseudo',
                    'class' => 'form-control')

               ))

            ->add('firstName','text', array(
                    'label'=> "Prénom",
                    'attr' => array(
                    'placeholder' => 'Votre prénom',
                    'class' => 'form-control')

                ))

            ->add('lastName','text', array(
                    'label'=> "Nom",
                    'attr' => array(
                    'placeholder' => 'Votre nom',
                    'class' => 'form-control')

                ))

            ->add('email', 'email', array(
                    'label'=> "Email",
                    'attr' => array(
                    'placeholder' => 'Votre email',
                    'class' => 'form-control')

                ))

            ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'Les mots de passe doivent correspondre',
                    'options' => array(
                    'required' => true),

                'first_options'  => array(
                    'label' => 'Mot de passe',
                    'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Votre mot de passe')),

                'second_options' => array(
                    'label' => 'Confirmez votre mot de passe',
                    'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Confirmez votre mot de passe')),

                ))

            ->add('adress','text', array(
                    'label' => 'Adresse',
                    'attr' => array(
                    'class' => 'form-control')
                ))

            ->add('phone', 'text', array(
                    'label' => 'Téléphone',
                    'attr' => array(
                    'placeholder' => 'Ex : 01 45 21 23 40',
                    'class' => 'form-control',
                    'maxlength'=>14)
                ))


            ->add('nextStep', 'submit', array(
                    'label' => 'Etape suivante',
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
        return 'bdloc_appbundle_register';
    }
}
