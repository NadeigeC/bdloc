<?php

namespace Bdloc\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DropSpotType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder


            ->add('dropSpot', 'entity', array(
                "label" => "Les points relais",
                    'class'=>'Bdloc\AppBundle\Entity\DropSpot',
                   'property'=>'fullAdress',
                   'empty_value' => 'Choisissez un point relais',


            ->add('nextStep','submit', array(
                "label" =>"Confirmation",
                'attr' => array('class'=>'btn btn-primary')
                ))

             /*->add('previousStep', 'submit', array(
                    'validation_groups' => false,
                ))*/
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
