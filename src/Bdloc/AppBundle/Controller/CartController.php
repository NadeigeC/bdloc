<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use \Wf3\AppBundle\Entity\Cart;
use \Wf3\AppBundle\Entity\CartItem;

class CartController extends Controller
{
    /**
     *
     * @Route("/panier/{id}")
     */
    public function findCartaction($id)

    {
        $params = array();

        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
      

        $cart = $cartRepo->find($id);
      

        $params = array (
            "cart" => $cart,
        
        );
        /*print_r($cart);
        die();*/

        return $this->render("cart/cart.html.twig",$params);
    }

     /**
     * @Route("/panier/retirer/{id}")
     * 
     */

     public function deleteItemaction($id){

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

     public function validCartaction($id){

        // Pénalités
        



      
        //Validation
        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
      

        $cart = $cartRepo->find($id);
        $cart->setStatus("valide");
      
        $em = $this->getDoctrine()->getManager();
        $em->flush();


     
       
        return $this->render("cart/valid.html.twig");

     }

 }