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
    use Bdloc\AppBundle\Form\RegisterType;
    use Bdloc\AppBundle\Util\StringHelper;
    use Bdloc\AppBundle\Form\ForgotPasswordType;
    use Bdloc\AppBundle\Form\NewPasswordType;
    use Bdloc\AppBundle\Form\UpdateProfileType;
    use Bdloc\AppBundle\Form\UpdatePasswordType;


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


                return $this->render("home.html.twig");
        }

            $params['registerForm'] = $registerForm->createView();

            return $this->render("user/register.html.twig", $params);

        }

        /**
    *@Route("/profile/{id}")
    */
    public function viewProfileAction($id, Request $request){

        //select
        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");

        //la méthode find() du repository s'attend à recevoir la clef primaire en paramètre
        $user = $userRepo->find($id);

        $params = array(
            "user" => $user);
       return $this->render("user/profile.html.twig", $params);

    }


        /**
        * @Route("/modifier-mon-profil/{id}")
        */
        public function updateProfileAction(Request $request, $id){

            $params = array();

            $user = $this->getUser();
            $updateProfileForm = $this->createForm(new UpdateProfileType(), $user, array('validation_groups' => array('updateProfile', 'Default')));

           //gère la soumission du form
            $request = $this->getRequest();
            $updateProfileForm->handleRequest($request);

            if ($updateProfileForm->isValid()){

            //on termine l'hydratation de notre objet User
            //avant enregistrement
            //salt, token, roles
            //dates directement dans l'entité avec les lifecyclecallbaks
                $user->getRoles();
                $user->getIsActive();
                $user->setDateModified( new \DateTime());
                $user->getDateCreated( new \DateTime());

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                'notice',
                'Données modifiées avec succès !'
                );


                return $this->redirect($this->generateUrl("bdloc_app_user_viewprofile", array('id' => $user->getId())));

        }

            $params['updateProfileForm'] = $updateProfileForm->createView();

            return $this->render("user/update_profile.html.twig", $params);

        }

        /**
        * @Route("/modifier-mon-mot-de-passe/{id}")
        */
        public function updatePasswordAction(Request $request, $id){

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

                return $this->redirect($this->generateUrl("bdloc_app_user_viewprofile", array('id' => $user->getId())));
            }
                $params['updatePasswordForm'] = $updatePasswordForm->createView();
                return $this->render("user/update_password.html.twig", $params);
        }


        /*public function geocodeAction(Request $request)
        {
            $result = $this->container
                ->get('bazinga_geocoder.geocoder')
                ->geocode($request->server->get('REMOTE_ADDR'));

            $body = $this->container
                ->get('bazinga_geocoder.dumper_manager')
                ->get('geojson')
                ->dump($result);

            $response = new Response();
            $response->setContent($body);

            return $response;
        }
*/
    }