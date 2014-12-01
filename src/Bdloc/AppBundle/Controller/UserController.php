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


    class UserController extends Controller {

        /**
        * @Route("/inscription")
        */

        public function registerAction(Request $request){

            $params = array();

            $user = new User();
            $registerForm = $this->createForm(new RegisterType(), $user);

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

                $user->setDropSpotId("2");

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


                //return $this->redirect( $this->generateUrl("bdloc_app_security_login"));
        }

            $params['registerForm'] = $registerForm->createView();

            return $this->render("user/register.html.twig", $params);

        }



    }