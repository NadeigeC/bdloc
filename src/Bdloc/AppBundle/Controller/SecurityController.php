<?php

    namespace Bdloc\AppBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Security\Core\SecurityContextInterface;
    use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
    use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

    use Bdloc\AppBundle\Entity\User;
    use Bdloc\AppBundle\Form\RegisterType;
    use Bdloc\AppBundle\Util\StringHelper;
    use Bdloc\AppBundle\Form\ForgotPasswordType;
    use Bdloc\AppBundle\Form\NewPasswordType;


    class SecurityController extends Controller {

        /**
        * @Route("/login")
        */

        public function loginAction(Request $request){

            $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContextInterface::AUTHENTICATION_ERROR
            );
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

        return $this->render(
            'security/login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error'         => $error,)
                );

        }

         /**
        * @Route("/oubli-du-mot-de-passe")
        */

        public function forgotPasswordAction(Request $request){

            $params = array();
            $user = new User();

            $forgotPasswordForm = $this->createForm(new ForgotPasswordType(), $user, array('validation_groups' => array('forgotPassword', 'Default')));

           //gère la soumission du form
            $request = $this->getRequest();
            $forgotPasswordForm->handleRequest($request);
            if ($forgotPasswordForm->isValid()){

                $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");

                $current_user = $userRepo->findOneByEmail($user->getEmail());

                /*print_r($current_user);*/


                $link = $this->generateUrl("bdloc_app_security_newpassword", array('token'=>$current_user->getToken(),'email'=>$current_user->getEmail()));

                //envoyer un mail
                $message = \Swift_Message::newInstance()
                ->setSubject("Reinitialisez votre mot de passe")
                ->setFrom('site@bdloc.com')
                ->setTo($current_user->getEmail())
                ->setContentType('text/html')
                ->setBody(
                    $this->renderView('emails/reinitialistaion_mot_de_passe.html.twig', array('user'=>$current_user))
                    )
                ;
                $this->get('mailer')->send($message);

                $request->getSession()->getFlashBag()->add(
                'notice',
                'Le message a bien été envoyé !'
                );

                return $this->redirect($this->generateUrl("bdloc_app_default_home"));
        }

            $params['forgotPasswordForm'] = $forgotPasswordForm->createView();

            return $this->render("security/forgot_password.html.twig", $params);

        }


         /**
        * @Route("/nouveau-mot-de-passe/{token}/{email}")
        */
        public function newPasswordAction(Request $request, $token, $email){

            $params = array();

            $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
            $user = $userRepo->findOneByEmail($email);

            $newPasswordForm = $this->createForm(new newPasswordType(), $user, array('validation_groups' => array('newPassword', 'Default')));

           //gère la soumission du form
            $request = $this->getRequest();
            $newPasswordForm->handleRequest($request);
            if ($newPasswordForm->isValid()){

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


                $request->getSession()->getFlashBag()->add(
                'notice',
                'Votre nouveau mot de passe est bien enregistré !'
                );

                return $this->redirect($this->generateUrl("bdloc_app_default_home"));

        }

            $params['newPasswordForm'] = $newPasswordForm->createView();

            return $this->render("security/new_password.html.twig", $params);

        }


    }

