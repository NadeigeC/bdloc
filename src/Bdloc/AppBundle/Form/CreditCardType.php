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
            ->add('paypalId', 'text', array(
                'label'=>'Identifiant Paypal'))
            ->add('validUntil', 'date', array(
                    'label'=>'Date d\'Expiration',
                    'format' =>'MMM-yyyy  d',
                    'years' => range(date('Y'), date('Y')+12),
                    'days' => array(1),
                    'empty_value' => array('year' => 'Année', 'month' => 'Mois', 'day' => false)))
            ->add('creditCardType','choice', array(
                'choices'   => array(
                'empty_value' => 'Type de carte',
                'mastercard' => 'MasterCard',
                 'visa' => 'Visa',
                 'american_express' => 'Amex',
                 'eurocard' => 'Eurocard',
                 )))
            ->add('cryptoCard', 'text', array(
                'label'=>'Cryptogramme'))
            ->add('ownerIdentity','text', array(
                'label'=>'Nom du propriétaire de la carte'))
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
