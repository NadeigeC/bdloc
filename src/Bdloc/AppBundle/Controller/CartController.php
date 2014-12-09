<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use \Bdloc\AppBundle\Entity\Cart;
use \Bdloc\AppBundle\Entity\CartItem;
use \DateTime;
use \DateInterval;




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

        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");

        $cart = $cartRepo->findOneBy(
             array('user'=>$user,'status'=>"courant")

        );

$nb=$cart->getCartItems();
$nb=count($nb);

         $params = array (
            "cart" => $cart,
            "nb" =>$nb,
        );

        return $this->render("cart/cart.html.twig",$params);


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

$book = $cartItem->getBook();
$book->setStock("1");
$em = $this->getDoctrine()->getManager();
$em->persist($book);
$em->flush();


        return $this->redirect($this->generateUrl("bdloc_app_cart_findcart",array('id' => $cartItem->getCart()->getId())) );


     }

     /**
     * @Route("/panier/validation/{id}")
     */

     public function validCartAction($id){

        // Pénalités

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

//$dateDelivery = new DateTime('2000-01-01');
//$dateDelivery->add(new DateInterval('P10D'));
//echo $date->format('Y-m-d') . "\n"; 
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


        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");

        $cart = $cartRepo->findBy(
             array('user'=>$user,'status'=>"courant")

        );




        $cartItemRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:CartItem");

        $cartItems = $cartItemRepo->findBy(
             array('cart'=>$cart)

        );

        $num=count($cartItems);


        $params = array (
            "num"   =>$num,

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

        $cart = $cartRepo->findOneBy(
             array('user'=>$user, 'status'=>'courant')

        );

        $cartItemRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:CartItem");

        $cartItems = $cartItemRepo->findBy(
             array('cart'=>$cart)


        );      



        /*for ($i=0;$i<count($cart);$i++){
            $cart[$i]->setStatus("valide");
        };*/

 $cart->setStatus("valide");
        $em = $this->getDoctrine()->getManager();
        $em->flush();



        /*for ($i=0;$i<count($cartItems);$i++){

            $book=$cartItems[$i]->getBook();
          
            $book->setStock("0");
            
        };*/

        $em = $this->getDoctrine()->getManager();
        $em->flush();

//$date = new DateTime('2000-01-01');
//$date->add(new DateInterval('P10D'));
//echo $date->format('Y-m-d') . "\n";        

         $params = array (
            'cart'=>$cart,
            
        );


        return $this->render("cart/validOrder.html.twig",$params);

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
$dateDelivery = new DateTime();
$dateDelivery=$dateDelivery->add(new DateInterval('P3D'));  
$dateReturn = new DateTime();
$dateReturn=$dateReturn->add(new DateInterval('P14D'));           
             $cart = new Cart();
             $cart -> setUser($user);
             $cart -> setStatus('courant');
             $cart ->setDateModified(new \DateTime());
             $cart ->setDateCreated(new \DateTime());
             $cart ->setDateDelivery($dateDelivery);
             $cart ->setDateReturn($dateReturn);
             $em = $this->getDoctrine()->getManager();
             $em->persist($cart);
             $em->flush();
        }

        $bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        $book = $bookRepo->findOneBy(
             array('id'=>$id, 'stock'=>'1')
        );



        $cartItemRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:CartItem");

        $cartItems = $cartItemRepo->findBy(
             array('cart'=>$cart)

        );

        $num=count($cartItems);


        if($num==10){

            $request = $this->getRequest();
            $request->getSession()->getFlashBag()->add(
                'notice',
                'Vous ne pouvez commander que 10 bds au maximum !'
            );

            return $this->redirect($this->generateUrl('bdloc_app_book_allbooks', array('page'=>1, 'nombreParPage'=> 12, 'direction'=> 'ASC', 'entity'=> 'dateCreated') ));
        }



    if(count($book)>0){
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


/*for ($i=0;$i<count($cartItems);$i++){

            $book=$cartItems[$i]->getBook();
          
            $book->setStock("0");
            $em = $this->getDoctrine()->getManager();
        $em->persist($book);
        $em->flush();
  };*/           

$book = $cartItem->getBook();
$book->setStock("0");
$em = $this->getDoctrine()->getManager();
$em->persist($book);
$em->flush();

}
       



       return $this->redirect($this->generateUrl('bdloc_app_book_allbooks', array('page'=>1, 'nombreParPage'=> 12, 'direction'=> 'ASC', 'entity'=> 'dateCreated') ));

    }

    /**
     * @Route("/livre/bouton/{id}")
     */

     public function inactiveButtonAction($id){
     
        $bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        $book = $bookRepo->find($id);

        $params = array(
            "book" => $book
        );

        return $this->render("cart/button.html.twig", $params);
    }

    /**
     * @Route("/livre/stock/{id}")
     */

     public function stockAction($id){
     
        $bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        $book = $bookRepo->find($id);

        $params = array(
            "book" => $book
        );

        return $this->render("cart/stock.html.twig", $params);
    }

    /**
     * @Route("/livre/texte/{id}")
     */

     public function textButtonAction($id){
     
        $bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        $book = $bookRepo->find($id);

        $params = array(
            "book" => $book
        );

        return $this->render("cart/textButton.html.twig", $params);
    }

 }

