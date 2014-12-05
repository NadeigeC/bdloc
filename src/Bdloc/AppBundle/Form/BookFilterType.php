<?php

namespace Bdloc\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\ORM\EntityRepository;

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
            ->add('entity', 'choice', array(
                    'choices'   => array('b.title' => 'Titre', 'b.dateCreated' => 'Date'),
                    "label" => "Trier par",
                    'mapped' => false
                ))
            ->add('direction', 'choice', array(
                    'choices'   => array('DESC' => 'DESC', 'ASC' => 'ASC'),
                    "label" => "Dans quel sens",
                    'mapped' => false
                ))
            ->add('nombre', 'choice', array(
                    'choices'   => array('12' => '12', '24' => '24', '36' => '36', '48' => '48', '60' => '60'),
                    "label" => "Combien par page",
                    'mapped' => false
                ))
            ->add('series', 'entity', array(
                    'class' => 'BdlocAppBundle:Serie',
                    'property' => 'style',
                    'expanded' => true,
                    'multiple' => true,
                    'mapped' => false,
                    'query_builder' => function( EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->orderBy('s.style', 'ASC')
                            ->groupBy('s.style');
                    },
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
