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
        
        //$nb=count($carts);
      
        //$output->writeln($nb);
 
        $dateNow=new \DateTime();  
        //$intervalMax=new \DateTime('00:10:00');

//initDate = new DateTime("2010/08/24");

    

        for ($i=0;$i<count($carts);$i++){ 
                    $carts[$i]->getDateCreated();
                    $carts[$i]->add(new DateInterval("PT1OM"));       
                    //$interval[$i] = $dateCreated->diff($dateNow);
                    //$output->writeln($interval[$i]);
                    if ($carts[i] < $dateNow){
                        $carts[$i]->setStatus("abandon");

                        $cartItems=$carts[$i]->getCartItems();

                            for ($i=0;$i<count($cartItems);$i++){
                                $book = $cartItems[$i]->getBook();
                                $book->setStock("1");
                            }

                    }
        }


        

       // for ($i=0;$i<count($carts);$i++){ 
        //    $carts[$i]->setStatus("abandon");
        //}

        //$em = $doctrine->getManager();
        //$em->flush();

       /* for ($i=0;$i<count($carts);$i++){

            $cartItems=$carts[$i]->getCartItems();

                for ($i=0;$i<count($cartItems);$i++){
                    $book = $cartItems[$i]->getBook();
                    $book->setStock("1");
                }
        }*/

        $em = $doctrine->getManager();
        $em->flush();

    }
}