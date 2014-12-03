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
    public function findCartAction($id)

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

     public function deleteItemAction($id){

        $cartItemrepo = $this->getDoctrine()->getRepository("BdlocAppBundle:CartItem");

        $cartItem = $cartItemrepo->find($id);
     
      
        $em = $this->getDoctrine()->getManager();
        $em -> remove( $cartItem );
        $em->flush();

        return $this->redirect($this->generateUrl("bdloc_app_cart_findcart",array('id' => $cartItem->getCart()->getId())) );

        
     }

     /**
     * @Route("/panier/validation/{id}/{user}")
     */

     public function validCartAction($id,$user){

        // Pénalités
        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $userRepo->find($user);

      
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
        
        return $this->render("cart/fine.html.twig",$params);

         }




        //Validation
        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
      

        $cart = $cartRepo->find($id);
        $cart->setStatus("valide");
      
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        /*print_r($cart);
        die();*/
     
        //Chercher le nombre d'de bd dans le panier.

         $params = array (
            "cart" => $cart,
            "user" => $user,
        
        );


        return $this->render("cart/valid.html.twig",$params);
       

     }

     /**
     * @Route("/panier/amende/{id}/{user}")
     */

      public function payFinesAction($user,$id){

        $params = array();
        $fineRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Fine");

        $fines = $fineRepo->findByUser($user);

        
        for ($i=0;$i<count($fines);$i++){
            $fines[$i]->setStatus("paye");
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirect($this->generateUrl("bdloc_app_cart_validcart",array('id' => $id,'user'=>$user)) );
     }

 }