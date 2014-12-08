<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use \Bdloc\AppBundle\Entity\Cart;
use \Bdloc\AppBundle\Entity\CartItem;
use \Bdloc\AppBundle\Form\DropSpotType;

class CartController extends Controller
{
    /**
     *
     * @Route("/panier/affiche")
     */
    public function findCartAction()

    {
        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $this->getUser();

        /*print_r($user);
        die();*/


        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");

        $cart = $cartRepo->findOneBy(
             array('user'=>$user,'status'=>"courant")

        );

         $params = array (
            "cart" => $cart,

        );

        return $this->render("cart/cart.html.twig",$params);





        /*$params = array();

        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");


        $cart = $cartRepo->findBy(
            array('id'=>$id,'status'=>"")
         );
        $params = array (
            "cart" => $cart,

        );
        /*print_r($cart);
        die();

        return $this->render("cart/cart.html.twig",$params);*/
    }

     /**
     * @Route("/panier/retirer/{id}")
     *
     */

     public function deleteItemAction($id){

        $cartItemrepo = $this->getDoctrine()->getRepository("BdlocAppBundle:CartItem");

        $cartItem = $cartItemrepo->find($id);


        $em = $this->getDoctrine()->getManager();
        $em -> remove( $cartItem );
        $em->flush();

        return $this->redirect($this->generateUrl("bdloc_app_cart_findcart",array('id' => $cartItem->getCart()->getId())) );


     }

     /**
     * @Route("/panier/validation/{id}")
     */

     public function validCartAction($id){

        // Pénalités
        /*$userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $userRepo->find($user);*/
        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $this->getUser();

        $params = array();
        $fineRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Fine");

        $fines = $fineRepo->findBy(
            array('user'=>$user,'status'=>'a payer')

        );


        $params = array (
            "fines" => $fines,
            "user"  => $user,
            "id"    => $id,
        );


       /*print_r($cart);
        die();*/

        if ($fines){


            $params = array();

            $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");


            $cart = $cartRepo->find($id);

            $total=0;

            for ($i=0;$i<count($fines);$i++){

            $montant=$fines[$i]->getMontant();
            $total=$total+$montant;
            }


             $params = array (
                "cart" => $cart,
                "fines" =>$fines,
                "user" =>$user,
                "total"=>$total,

            );


        return $this->render("cart/fine.html.twig",$params);

         }




        //Validation
        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");


        $cart = $cartRepo->find($id);
        //$cart->setStatus("valide");


        //$em = $this->getDoctrine()->getManager();
        //$em->flush();

        /*print_r($cart);
         die();*/

         //Calcul date de livraison
         $dateCreated=$cart->getDateCreated();

        /*$dateDelivery = date('Y-m-d', strtotime($dateCreated.' +15 days'));
         print_r($dateDelivery);
         die();*/
        /*$date=new DateTime($dateCreated);
         $date->\DateTime add ( DateInterval ('P12M') );
          print_r($date);
         die();*/

        //Chercher le nombre d'de bd dans le panier.

         $params = array (
            "cart" => $cart,
            "user" => $user,
        );


        return $this->render("cart/order.html.twig",$params);


     }

     /**
     * @Route("/panier/amende/{id}")
     */

      public function payFinesAction($id){

        $params = array();

        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $this->getUser();

        $fineRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Fine");

        $fines = $fineRepo->findByUser($user);


        for ($i=0;$i<count($fines);$i++){
            $fines[$i]->setStatus("paye");
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirect($this->generateUrl("bdloc_app_cart_validcart",array('id' => $id,'user'=>$user)) );
     }

     /**
     * @Route("/panier/list/{id}")
     */
     public function seeListAction($id){
         $params = array();

        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");

        $cart = $cartRepo->findOneBy(
                array('id'=>$id,'status'=>"courant")
         );
        $params = array (
            "cart" => $cart,

        );

        return $this->render("cart/list.html.twig",$params);

    }

     /**
     * @Route("/panier")
     *
     */

     public function CountItemAction(){

        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $this->getUser();

        /*print_r($user);
        die();*/


        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");

        $cart = $cartRepo->findBy(
             array('user'=>$user,'status'=>"courant")

        );




        $cartItemRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:CartItem");

        $cartItems = $cartItemRepo->findBy(
             array('cart'=>$cart)

        );

        $num=count($cartItems);
        /*echo($num);
        die();*/

        $params = array (
            //"cart"     => $cart,
            "num"   =>$num,
            //'cartItem' => $cartItems,
            //'user'=>$user,


        );




       return $this->render("cart/countItem.html.twig",$params);


     }

      /**
     * @Route("/commande/validation/{id}")
     */

     public function validOrderAction($id){

        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $this->getUser();


        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");

        $cart = $cartRepo->findBy(
             array('user'=>$user)

        );

        for ($i=0;$i<count($cart);$i++){
            $cart[$i]->setStatus("valide");
        }


        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->render("cart/validOrder.html.twig");

    }

    /**
     * @Route("/panier/ajouter/{id}")
     */

     public function addAction($id){
        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $this->getUser();

        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $cart = $cartRepo->findOneBy(
             array('user'=>$user, 'status'=>'courant')
        );

        if(count($cart)==0){
             $cart = new Cart();
             $cart -> setUser($user);
             $cart -> setStatus('courant');
             $cart ->setDateModified(new \DateTime());
             $cart ->setDateCreated(new \DateTime());
             $cart ->setDateDelivery(new \DateTime());
             $em = $this->getDoctrine()->getManager();
             $em->persist($cart);
             $em->flush();
        }

        $bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        $book = $bookRepo->findOneBy(
             array('id'=>$id)
        );

        $cartItem = new CartItem();

        $cartItem -> setCart($cart);
        $cartItem -> setBook($book);
        $cartItem ->setDateModified(new \DateTime());
        $cartItem ->setDateCreated(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($cartItem);
        $em->flush();

        $params = array (
            'id'=>$id
        );

       //return $this->render("cart/addCart.html.twig");

       return $this->redirect($this->generateUrl('bdloc_app_book_allbooks', array('page'=>1, 'nombreParPage'=> 12, 'direction'=> 'ASC', 'entity'=> 'dateCreated') ));

    }
 }

