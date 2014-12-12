<?php

namespace Bdloc\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                'attr' => array('class' => 'form-control','rows' => '10'),
                'mapped'=>false,
                'label' => false,
                'constraints' => array(
                   new NotBlank(array('message'=>'Entrez un message')),
                   new Length(array('min' => 3))
                    )))

            ->add('captcha', 'captcha', array(
                        'width' => 200,
                        'height' => 50,
                        'length' => 8,
                        'quality' => 100,
                        'invalid_message' => 'Mauvais code',
                        'as_url' => true,
                        'reload' => true,
                    ))

            ->add('submit', 'submit', array(
                "label" => "Quitter BDLOC !",
                'attr' => array(
                'class' => 'btn red-button btn-default-red')
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
