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

                    'label'=>'Identifiant Paypal',
                    'attr' => array(
                    'class' => 'form-control')
                ))

            ->add('validUntil', 'date', array(
                    'label'=>false,
                    'format' =>'MMM-yyyy  d',
                    'years' => range(date('Y'), date('Y')+12),
                    'days' => array(1),

                    'empty_value' => array(
                    'year' => 'Année',
                    'month' => 'Mois',
                    'day' => false),
                ))

            ->add('creditCardType','choice', array(
                    'label'=>'Type de carte',
                    'attr' => array(
                    'class' => 'form-control'),
                    'choices'   => array(
                    'empty_value' => 'Type de carte',
                    'mastercard' => 'MasterCard',
                    'visa' => 'Visa',
                    'american_express' => 'Amex',
                    'eurocard' => 'Eurocard')
                 ))

            ->add('cryptoCard', 'text', array(
                    'label'=>'Cryptogramme',
                    'attr' => array(
                    'class' => 'form-control',
                    'maxlength'=>3)
                ))

            ->add('ownerIdentity','text', array(
                    'label'=>'Nom du propriétaire de la carte',
                    'attr' => array(
                    'class' => 'form-control')
                ))


            ->add('submit','submit', array(
                    'label' =>"Confirmation",
                    'attr' => array('class'=>'btn red-button btn-default-red')
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
