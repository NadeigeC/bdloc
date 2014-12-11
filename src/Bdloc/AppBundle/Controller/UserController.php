<?php

    namespace Bdloc\AppBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Security\Core\SecurityContextInterface;
    use Symfony\Component\EventDispatcher\EventDispatcher;
    use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
    use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
    use Symfony\Component\HttpFoundation\RedirectResponse; // N'oubliez pas ce use
    use Symfony\Component\HttpFoundation\Response;

    use Bdloc\AppBundle\Entity\User;
    use Bdloc\AppBundle\Entity\DropSpot;
    use Bdloc\AppBundle\Entity\CreditCard;
    use Bdloc\AppBundle\Entity\Cart;
    use Bdloc\AppBundle\Entity\Fine;
    use Bdloc\AppBundle\Form\RegisterType;
    use Bdloc\AppBundle\Form\DropSpotType;
    use Bdloc\AppBundle\Form\CreditCardType;
    use Bdloc\AppBundle\Util\StringHelper;
    use Bdloc\AppBundle\Form\ForgotPasswordType;
    use Bdloc\AppBundle\Form\NewPasswordType;
    use Bdloc\AppBundle\Form\UpdateProfileType;
    use Bdloc\AppBundle\Form\UpdatePasswordType;
    use Bdloc\AppBundle\Form\UpdateDropSpotType;
    use Bdloc\AppBundle\Form\QuitBdlocType;


    class UserController extends Controller {

        /**
        * @Route("/inscription")
        */

        public function registerAction(Request $request){

            $params = array();

            $user = new User();
            $registerForm = $this->createForm(new RegisterType(), $user, array('validation_groups' => array('registration', 'Default')));

           //gère la soumission du form
            $request = $this->getRequest();
            $registerForm->handleRequest($request);

            if ($registerForm->isValid()){
            //on termine l'hydratation de notre objet User
            //avant enregistrement
            //salt, token, roles
            //dates directement dans l'entité avec les lifecyclecallbaks
                $user->setRoles( array("ROLE_USER"));
                $user->setIsActive(1);

                $user->setDateModified( new \DateTime());
                $user->setDateCreated( new \DateTime());

                $stringHelper = new StringHelper();

                $user->setSalt($stringHelper->randomString());
                $user->setToken($stringHelper->randomString(30));

                $factory = $this->get('security.encoder_factory');

                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($user->getPassword(),$user->getSalt());
                $user->setPassword($password);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();


                //CONNEXION AUTOMATIQUE
                //tiré de http://stackoverflow.com/questions/9550079/how-to-programmatically-login-authenticate-a-user
                // "secured_area" est le nom du firewall défini dans security.yml
                $token = new UsernamePasswordToken($user, $user->getPassword(), "secured_area", $user->getRoles());
                $this->get("security.context")->setToken($token);

                //déclanche l'évènement de login
                $event = new InteractiveLoginEvent($request, $token);
                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

                return $this->redirect( $this->generateUrl("bdloc_app_user_dropspot"));
        }

            $params['registerForm'] = $registerForm->createView();


            return $this->render("user/register.html.twig", $params);

        }

    /**
        * @Route("/inscription/point-relais")
    */

    public function dropSpotAction(Request $request){

            $params = array();

            $user = $this->getUser();
            $dropSpotForm = $this->createForm(new dropSpotType(), $user, array('validation_groups' => array('registration', 'Default')));

           //gère la soumission du form
            $request = $this->getRequest();
            $dropSpotForm->handleRequest($request);
            //$map = $this->get('ivory_google_map.map');

            if ($dropSpotForm->isValid()){
                $user->setDateModified( new \DateTime());
                $user->setDateCreated( new \DateTime());

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return $this->redirect( $this->generateUrl("bdloc_app_user_creditcard"));
        }

            $params['dropSpotForm'] = $dropSpotForm->createView();

            return $this->render("user/dropspot.html.twig", $params);

        }

    /**
    * @Route("/inscription/carte-de-credit/")
    */

    public function creditCardAction(Request $request){

            $params = array();

            $user = $this->getUser();

            $creditCard = new CreditCard();
            $creditCardForm = $this->createForm(new CreditCardType(), $creditCard, array('validation_groups' => array('registration', 'Default')));


           //gère la soumission du form
            $request = $this->getRequest();
            $creditCardForm->handleRequest($request);

            if ($creditCardForm->isValid()){

                $creditCard->setDateModified( new \DateTime());
                $creditCard->setDateCreated( new \DateTime());

                $em = $this->getDoctrine()->getManager();
                $creditCard->setUser($user);
                $em->persist($creditCard);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                'notice',
                'Vous êtes désormais abonné à BDLOC !'
                );

                return $this->redirect( $this->generateUrl("bdloc_app_paypal_takesubscriptionpayment"));
                // return $this->redirect($this->generateUrl("bdloc_app_book_allbooks", array('page'=>1, 'nombreParPage'=> 12, 'direction'=> 'ASC', 'entity'=> 'dateCreated')));
        }

            $params['creditCardForm'] = $creditCardForm->createView();

            return $this->render("user/credit_card.html.twig", $params);

        }

    /**
    *@Route("/profile")
    */
    public function viewProfileAction(Request $request){

        $user = $this->getUser();

        $fineRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Fine");
        $fines = $fineRepo->findBy(
            array('user'=>$user,'status'=>'a payer'));

        $params = array(
            "user" => $user,
            "fines" => $fines);

       return $this->render("user/profile.html.twig", $params);

        }


    /**
    * @Route("/modifier-mon-profil")
    */
    public function updateProfileAction(Request $request){

        $params = array();

        $user = $this->getUser();
        $updateProfileForm = $this->createForm(new UpdateProfileType(), $user, array('validation_groups' => array('updateProfile', 'Default')));

       //gère la soumission du form
        $request = $this->getRequest();
        $updateProfileForm->handleRequest($request);

        if ($updateProfileForm->isValid()){

            $user->setDateModified( new \DateTime());
            $user->getDateCreated( new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
            'notice',
            'Données modifiées avec succès !'
            );

            return $this->redirect($this->generateUrl("bdloc_app_user_viewprofile"));
    }

        $params['updateProfileForm'] = $updateProfileForm->createView();

        return $this->render("user/update_profile.html.twig", $params);

    }

        /**
        * @Route("/modifier-mon-mot-de-passe")
        */
        public function updatePasswordAction(Request $request){

            $params = array();

            $user = $this->getUser();
            $updatePasswordForm = $this->createForm(new UpdatePasswordType(), $user, array('validation_groups' => array('updatePassword', 'Default')));

            //gère la soumission du form
            $request = $this->getRequest();
            $updatePasswordForm->handleRequest($request);

            if ($updatePasswordForm->isValid()){
                $stringHelper = new StringHelper();

                $user->setSalt($stringHelper->randomString());
                $user->setToken($stringHelper->randomString(30));

                $factory = $this->get('security.encoder_factory');

                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($user->getPassword(),$user->getSalt());
                $user->setPassword($password);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                'notice',
                'Votre nouveau mot de passe est bien enregistré !'
                );

                return $this->redirect($this->generateUrl("bdloc_app_user_viewprofile"));
            }
                $params['updatePasswordForm'] = $updatePasswordForm->createView();
                return $this->render("user/update_password.html.twig", $params);
        }


        /**
        * @Route("/modifier-point-relais")
        */
        public function updateDropSpotAction(Request $request){

            $params = array();
            $user = $this->getUser();
            $dropSpotForm = $this->createForm(new DropSpotType(), $user, array('validation_groups' => array('dropSpot', 'Default')));
            $referer = $request->headers->get('referer');
            $dropSpotForm->get('redirect')->setData($referer);

            //gère la soumission du form
            $request = $this->getRequest();
            $dropSpotForm->handleRequest($request);

            if ($dropSpotForm->isValid()){

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                'notice',
                'Nouveau point relais sauvegardé !');

                return $this->redirect($dropSpotForm->get('redirect')->getData());
            }

                $params['dropSpotForm'] = $dropSpotForm->createView();
                return $this->render("user/update_dropspot.html.twig", $params);
        }


    /**
    * @Route("/modifier-carte-de-credit")
    */
    public function updateCreditCardAction(Request $request){

            $params = array();

            $user = $this->getUser();

            $creditCard = $user->getCreditCard();
            $creditCardForm = $this->createForm(new CreditCardType(), $creditCard, array('validation_groups' => array('registration', 'Default')));


           //gère la soumission du form
            $request = $this->getRequest();
            $creditCardForm->handleRequest($request);

            if ($creditCardForm->isValid()){

                $creditCard->setDateModified( new \DateTime());
                $creditCard->setDateCreated( new \DateTime());

                $em = $this->getDoctrine()->getManager();
                $creditCard->setUser($user);
                $em->persist($creditCard);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                'notice',
                'Nouvelles information de paiement sauvegardées !');

                return $this->redirect($this->generateUrl("bdloc_app_user_viewprofile"));
        }

            $params['creditCardForm'] = $creditCardForm->createView();

            return $this->render("user/credit_card.html.twig", $params);

        }


    /**
    *@Route("/historique-de-location")
    */
    public function rentalHistoryAction(){

        $params = array();
        $user = $this->getUser();

        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $cart = $cartRepo->findBy(array('user'=>$user, 'status'=>'valide'),array('dateModified'=>'DESC'),5);


        $params = array (
            "carts" => $cart,
            "user" => $user);

        return $this->render("user/rental_history.html.twig", $params);

        }

    /**
     * @Route("/voir-mes-amendes")
     */
     public function viewFinesAction(){

        // Pénalités
        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $this->getUser();

        $params = array();
        $fineRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Fine");

        $fines = $fineRepo->findBy(
            array('user'=>$user,'status'=>'a payer'));

        $params = array (
            "fines" => $fines,
            "user"  => $user,
        );

        if ($fines){

            $params = array();
            $total=0;

            for ($i=0;$i<count($fines);$i++){

            $montant=$fines[$i]->getMontant();
            $total=$total+$montant;
            }

             $params = array (
                "fines" =>$fines,
                "user" =>$user,
                "total"=>$total);

        }

            return $this->render("user/view_fines.html.twig",$params);
    }


     /**
     * @Route("/payer-mes-amendes")
     */

      public function payFinesAction(Request $request){

        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $this->getUser();

        $fineRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Fine");
        $fines = $fineRepo->findByUser($user);


        for ($i=0;$i<count($fines);$i++){
            $fines[$i]->setStatus("paye");
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $request->getSession()->getFlashBag()->add(
        'notice',
        'Vous avez bien payé vos amendes !'
        );

        return $this->redirect($this->generateUrl("bdloc_app_user_viewprofile",array('user'=>$user)) );
     }


    /**
    * @Route("/desabonnement")
    */
    public function quitBdlocAction(Request $request){

        $params = array();

            $user = $this->getUser();
            $quitBdlocForm = $this->createForm(new QuitBdlocType(), $user);

         //gère la soumission du form
            $request = $this->getRequest();
            $quitBdlocForm->handleRequest($request);

            if ($quitBdlocForm->isValid()){
                $user->setDateModified( new \DateTime());
                $user->setDateCreated( new \DateTime());
                $user->setIsActive(0);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                //envoyer un mail
                $message = \Swift_Message::newInstance()
                ->setSubject("Désabonnement de BDLOC")
                ->setFrom('site@bdloc.com')
                ->setTo('nadeige.pirot@gmail.com', $user->getEmail())
                ->setContentType('text/html')
                ->setBody(
                    $this->renderView('emails/desabonnement.html.twig', array('user'=>$user, 'raisons'=>$quitBdlocForm->get('raisons')->getData()))
                    )
                ;
                $this->get('mailer')->send($message);


                return $this->redirect($this->generateUrl("logout"));
        }
                $params['quitBdlocForm'] = $quitBdlocForm->createView();
                return $this->render("user/quit_bdloc.html.twig", $params);

}

}