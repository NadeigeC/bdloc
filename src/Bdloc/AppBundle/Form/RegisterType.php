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
                 "label" => "votre pseudo",
                 'attr' => array(
                    'placeholder' => 'Pseudo',
                    'class' => 'form-control')
               ))

            ->add('firstName','text', array(
                "label" => "votre prénom",
                'attr' => array(
                    'placeholder' => 'Prénom',
                    'class' => 'form-control')
                ))

            ->add('lastName','text', array(
                "label" => "votre nom",
                'attr' => array(
                    'placeholder' => 'Nom',
                    'class' => 'form-control')
                ))

            ->add('email', 'email', array(
                "label" => "votre email",
                'attr' => array(
                    'placeholder' => 'Email',
                    'class' => 'form-control')
                ))

            ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'Les mots de passe doivent correspondre',
                    'options' => array('required' => true),
                    'first_options'  => array('label' => 'Mot de passe'),
                    'second_options' => array('label' => 'Mot de passe (validation)'),
                    'attr' => array('class'=>'form-control')
                    ))

            ->add('adress','text', array(
                 'attr' => array(
                    'class' => 'form-control')))

            ->add('phone', 'text', array(
                    'attr' => array(
                    'placeholder' => 'Ex : 01 45 21 23 40',
                    'class' => 'form-control')
                    ))

            ->add('dropSpot', 'entity', array(
                    'class'=>'Bdloc\AppBundle\Entity\DropSpot',
                   'property'=>'fullAdress',
                   'empty_value' => 'Choisissez un point relais',
                   'attr' => array(
                       'class' => 'form-control')))


            ->add('submit','submit', array(
                "label" =>"Inscription",
                'attr' => array('class'=>'btn btn-primary')
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
