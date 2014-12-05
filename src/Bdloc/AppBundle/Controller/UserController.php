<?php

    namespace Bdloc\AppBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Security\Core\SecurityContextInterface;
    use Symfony\Component\EventDispatcher\EventDispatcher;
    use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
    use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

    use Bdloc\AppBundle\Entity\User;
    use Bdloc\AppBundle\Entity\DropSpot;
    use Bdloc\AppBundle\Entity\CreditCard;
    use Bdloc\AppBundle\Form\RegisterType;
    use Bdloc\AppBundle\Form\DropSpotType;
    use Bdloc\AppBundle\Form\CreditCardType;
    use Bdloc\AppBundle\Util\StringHelper;
    use Bdloc\AppBundle\Form\ForgotPasswordType;
    use Bdloc\AppBundle\Form\NewPasswordType;
    use Bdloc\AppBundle\Form\UpdateProfileType;
    use Bdloc\AppBundle\Form\UpdatePasswordType;
    use Bdloc\AppBundle\Form\UpdateDropSpotType;


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

                return $this->redirect($this->generateUrl("bdloc_app_default_home"));
        }

            $params['creditCardForm'] = $creditCardForm->createView();

            return $this->render("user/credit_card.html.twig", $params);

        }

    /**
    *@Route("/profile/{id}")
    */
    public function viewProfileAction(Request $request, $id){

        //select
        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");

        //la méthode find() du repository s'attend à recevoir la clef primaire en paramètre
        $user = $userRepo->find($id);

        $params = array(
            "user" => $user);
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

                return $this->redirect($this->generateUrl("bdloc_app_user_viewprofile"));
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


}