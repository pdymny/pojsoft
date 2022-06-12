<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

// klasy
use App\Entity\User;
use App\Entity\UserNotify;
use App\Entity\Partners;
use App\Entity\PartnersHistory;

// formularze
use App\Form\ForgotPasswordType;
use App\Form\RegisterType;


class SecurityController extends AbstractController
{

    // dodanie notice
    public function addNote($text, $user, $request) {
        $entityManager = $this->getDoctrine()->getManager();
        $ip = $request->getClientIp();

        $note = new UserNotify();
        $note->setUser($user);
        $note->setDate(new \DateTime());
        $note->setIp($ip);
        $note->setText($text);
        $entityManager->persist($note);
        $entityManager->flush();
    }

    /**
     * @Route("/", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('start/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    /**
     * @Route("/user/settings/edit", name="editUser")
     */
    public function editUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $request->request->get('settings');

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($this->getUser());

        if (!$user) {
            $this->addFlash('danger', 'Nie istenieje taki użytkownik.');
            return $this->redirectToRoute('settings');
        }

        $user->setNameCompany($form['name_company']);
        $user->setStreet($form['street']);
        $user->setCodePost($form['code']);
        $user->setCity($form['city']);
        $user->setNip($form['nip']);
        $user->setRegon($form['regon']);

        $user->setFirstname($form['firstname']);
        $user->setName($form['name']);
        $user->setEmail($form['email']);
        $user->setPhone($form['phone']);

        if(!empty($form['old_password'])) {

            $password_test = $passwordEncoder->isPasswordValid($user, $form['old_password']);

            if($password_test == true) {

                $new_password = $passwordEncoder->encodePassword($user, $form['new_password']);
                $user->setPassword($new_password);
            } else {
                $this->addFlash('danger', 'Niestety, ale stare hasło nie jest takie samo.');
                return $this->redirectToRoute('settings');           
            }
        }

        $entityManager->flush();

        $text = "Zmieniono dane dla tego konta.";
        $this->addNote($text, $this->getUser(), $request);

        $this->addFlash('success', 'Poprawnie zmieniono dane.');
        return $this->redirectToRoute('settings');
    }

    /**
     * @Route("/forgot/password", name="forgot_password")
     */
    public function forgotAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer)
    {
        $user = new User();
        $form = $this->createForm(ForgotPasswordType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $base_user = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
        
            $new_password = $user->newPassword();

            $newEncodedPassword = $passwordEncoder->encodePassword($base_user, $new_password);
            $base_user->setPassword($newEncodedPassword);
                
            $entityManager->persist($base_user);
            $entityManager->flush();

            $from = $this->request->server->get('E_MAIL');

            $email = (new TemplatedEmail())
                ->from($from)
                ->to($base_user->getEmail())
                ->subject('Przypomnienie hasła do panelu DymCode.')
                ->htmlTemplate('emails/forgot.html.twig')
                ->context([
                    'user' => $base_user,
                    'password' => $new_password,
                ]);

            $sentEmail = $mailer->send($email);
            $messageId = $sentEmail->getMessageId();

            if($messageId > 0) {
                $text = "Wysłano przypomnienie hasła dla tego konta.";
                $this->addFlash('success', 'Nowe hasło wysłano na e-maila.');
            } else {
                $text = "Wysłano przypomnienie hasła dla tego konta. Błąd wysłania wiadomości e-mail.";
                $this->addFlash('danger', 'Prosimy o powtórzenie próby wysłania hasła lub skontaktowanie się z administracją serwisu.');
            }

            $this->addNote($text, $base_user, $request);
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('start/forgot.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerUser(Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer)
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $this->registerForm($request, $passwordEncoder, $mailer, $user, $form);

        return $this->render('start/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function registerForm($request, $passwordEncoder, $mailer, $user, $form)
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $base_user = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail(), 'nip' => $user->getNip()]);

            if ($base_user) {
                $this->addFlash('danger', 'Już istenieje użytkownik o takim adresie e-mail.');
            } else {
                $ip = $request->getClientIp();

                $partner = $request->cookies->get('dc_partner');
                if($partner) {
                    $base_partner = $entityManager->getRepository(Partners::class)->findOneBy(array('code' => $partner));

                    $history = new PartnersHistory();
                    $history->setMoney(0);
                    $history->setAmount($base_partner->getAmount());
                    $history->setDate(new \DateTime());
                    $history->setPartner($base_partner);
                    $history->setStatus(1);
                    $entityManager->persist($history);

                    $entityManager->flush();
                }

                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
                $user->setIp($ip);
                $user->setToken();
                $user->setActive(0);
                $user->setRole(0);
                if($partner) {
                    $user->setCodePartner($partner);
                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                
                $from = $request->server->get('E_MAIL');

                $email = (new TemplatedEmail())
                    ->from($from)
                    ->to($user->getEmail())
                    ->subject('Dziękujemy za rejestrację w panelu firmy DymCode. Aktywuj konto poprzez podany link.')
                    ->htmlTemplate('emails/register.html.twig')
                    ->context([
                        'user' => $user,
                    ]);

                $sentEmail = $mailer->send($email);
                $messageId = $sentEmail->getMessageId();

                if($messageId > 0) {
                    $text = "Założono to konto.";
                    $this->addFlash('success', 'Założono poprawnie konto. Poczekaj na e-mail aktywacyjny.');
                } else {
                    $text = "Założono konto to konto. Błąd wysłania e-maila.";
                    $this->addFlash('danger', 'Założono konto lecz nie można było wysłać wiadomości e-mail.');
                }

                $this->addNote($text, $user, $request);
            }
        }
    }    
}
