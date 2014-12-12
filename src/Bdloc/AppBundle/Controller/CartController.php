<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use \Bdloc\AppBundle\Entity\Cart;
use \Bdloc\AppBundle\Entity\CartItem;
use \DateTime;
use \DateInterval;




class CartController extends Controller {

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

        if (! $cart){
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
        };

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


        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $cart = $cartRepo->find($id);

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

        //see kmj/paypalbridgebundle
        $apiContext = $this->get('paypal')->getApiContext();

       // ### CreditCard
       // A resource representing a credit card that can be
       // used to fund a payment.
       $card = new CreditCard();
       $card->setType("visa");
       $card->setNumber("4417119669820331");
       $card->setExpire_month("11");
       $card->setExpire_year("2018");
       $card->setCvv2("987");
       $card->setFirst_name("Joe");
       $card->setLast_name("Shopper");

       // ### FundingInstrument
       // A resource representing a Payer's funding instrument.
       // Use a Payer ID (A unique identifier of the payer generated
       // and provided by the facilitator. This is required when
       // creating or using a tokenized funding instrument)
       // and the `CreditCardDetails`
       $fi = new FundingInstrument();
       $fi->setCredit_card($card);

       // ### Payer
       // A resource representing a Payer that funds a payment
       // Use the List of `FundingInstrument` and the Payment Method
       // as 'credit_card'
       $payer = new Payer();
       $payer->setPayment_method("credit_card");
       $payer->setFunding_instruments(array($fi));

       // ### Amount
       // Let's you specify a payment amount.
       $amount = new Amount();
       $amount->setCurrency("EUR");
       $amount->setTotal("120.00");

       // ### Transaction
       // A transaction defines the contract of a
       // payment - what is the payment for and who
       // is fulfilling it. Transaction is created with
       // a `Payee` and `Amount` types
       $transaction = new Transaction();
       $transaction->setAmount($amount);
       $transaction->setDescription("Abonnement");

       // ### Payment
       // A Payment Resource; create one using
       // the above types and intent as 'sale'
       $payment = new Payment();
       $payment->setIntent("sale");
       $payment->setPayer($payer);
       $payment->setTransactions(array($transaction));

       
    

        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $this->getUser();

        $fineRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Fine");
        $fines = $fineRepo->findByUser($user);


        

        

        // ### Create Payment
       // Create a payment by posting to the APIService
       // using a valid ApiContext
       // The return object contains the status;
        try {
            $result = $payment->create($apiContext);

            for ($i=0;$i<count($fines);$i++){
            $fines[$i]->setStatus("paye");
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

            return $this->redirect($this->generateUrl("bdloc_app_cart_validcart",array('id' => $id,'user'=>$user)) );

        } catch (\Paypal\Exception\PPConnectionException $pce) {
            print_r( json_decode($pce->getData()) );
        }

        die();
     }



     /**
     * @Route("/panier/list/{id}")
     */
     public function seeListAction($id){

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
     * @Route("/panier/nombre_articles")
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


        $cart->setStatus("valide");
        $em = $this->getDoctrine()->getManager();
        $em->flush();

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


            $referer = $this->getRequest()->headers->get('referer');

            return $this->redirect($referer);

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


        $book = $cartItem->getBook();
        $book->setStock("0");
        $em = $this->getDoctrine()->getManager();
        $em->persist($book);
        $em->flush();

        }

        $referer = $this->getRequest()->headers->get('referer');

        return $this->redirect($referer);
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

        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $this->getUser();

        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $cart = $cartRepo->findOneBy(
             array('user'=>$user, 'status'=>'courant', )
        );

        $cartItemRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:CartItem");

        $cartItem = $cartItemRepo->findOneBy(
             array('cart'=>$cart, 'book'=>$book)

        );

        $cartItem = count($cartItem);

        $params = array(
            "book" => $book,
            "cartItem"=>$cartItem,
            "cart" =>$cart,          
        );

        return $this->render("cart/textButton.html.twig", $params);
    }





 }

