<?php

namespace Bdloc\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreditCardType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('paypalId')
            ->add('validUntil', 'date', array(
                    'format' =>'MMM-yyyy  d',
                    'years' => range(date('Y'), date('Y')+12),
                    'days' => array(1),
                    'empty_value' => array('year' => 'Select Year', 'month' => 'Select Month', 'day' => false)))
            ->add('creditCardType','choice', array(
                'choices'   => array(
                'empty_value' => 'Type de carte',
                'mastercard' => 'MasterCard',
                 'visa' => 'Visa',
                 'american_express' => 'Amex',
                 'eurocard' => 'Eurocard',
                 )))
            ->add('cryptoCard')
            ->add('ownerIdentity')
            ->add('submit','submit', array(
                "label" =>"Confirmation",
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
            'data_class' => 'Bdloc\AppBundle\Entity\CreditCard'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bdloc_appbundle_creditcard';
    }
}
