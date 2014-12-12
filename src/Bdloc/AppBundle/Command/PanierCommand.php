<?php

namespace Bdloc\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use \Bdloc\AppBundle\Entity\Cart;

class PanierCommand extends ContainerAwareCommand
{
    protected function configure(){
        $this
            ->setName('bdloc:panier')
            ->setDescription('Durée de vie du panier')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $doctrine = $this->getContainer()->get("doctrine");
        

        $cartRepo = $doctrine->getRepository("BdlocAppBundle:Cart");
        $carts = $cartRepo->findBy(
             array('status'=>'courant')
        );
        
     
        $dateNow=new \DateTime();  
        



        for ($i=0;$i<count($carts);$i++){ 
                    $dateCreated = $carts[$i]->getDateCreated();
                   $dateCreated->add(new \DateInterval("PT10M"));       
                   
                    if ($dateCreated< $dateNow){
                        $carts[$i]->setStatus("abandon");

                        $cartItems=$carts[$i]->getCartItems();

                            for ($i=0;$i<count($cartItems);$i++){
                                $book = $cartItems[$i]->getBook();
                                $book->setStock("1");
                            }

                    }
        }
     
    

        $em = $doctrine->getManager();
        $em->flush();

    }
}