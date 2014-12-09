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
       

        $nb=count($carts);
      
        $output->writeln($nb);

        for ($i=0;$i<count($carts);$i++){ 
            $carts[$i]->setStatus("abandon");
        }
 $em = $doctrine->getManager();
        $em->flush();

        for ($i=0;$i<count($carts);$i++){

            $cartItems=$carts[$i]->getCartItems();

                for ($i=0;$i<count($cartItems);$i++){
                    $book = $cartItems[$i]->getBook();
                    $book->setStock("1");
                }
        }

        $em = $doctrine->getManager();
        $em->flush();

    }
}