<?php

namespace Bdloc\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BookFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('dateCreated', 'choice', array(
                    'choices'   => array('d' => 'DESC', 'a' => 'ASC'),
                    'required'  => false,
                    "label" => "Trier par date"
                ))
            ->add('title', 'choice', array(
                    'choices'   => array('d' => 'DESC', 'a' => 'ASC'),
                    'required'  => false,
                    "label" => "Trier par ordre alphabétique"
                ))
            ->add('submit', 'submit', array(
                "label" => "Trier"
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bdloc\AppBundle\Entity\Book'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bdloc_appbundle_bookfilter';
    }
}
