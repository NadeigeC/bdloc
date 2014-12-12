<?php

namespace Bdloc\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuitBdlocType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('raisons', 'textarea', array(
                'attr' => array('class' => 'form-control captcha','rows' => '10'),
                'mapped'=>false,
                'label' => false))

            ->add('captcha', 'captcha', array(
                "label" => "captcha",
                'attr' => array(
                'class' => 'form-control captcha')
                    ))

            ->add('submit', 'submit', array(
                "label" => "Quitter BDLOC !",
                'attr' => array(
                'class' => 'btn red-button btn-default-red ')
                    ));
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
