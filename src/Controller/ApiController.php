<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use App\Entity\User;
use App\Entity\Help;


class ApiController extends AbstractController
{

    /**
     * @Route("/api/mail/send/forgot", name="api_mail_send_forgot")
     */
    public function mailSendForgot(Request $request)
    {
    	$id = $request->query->get('id');
    	$token = $request->query->get('token');
    	$send_email = $request->query->get('email');
    	$password = $request->query->get('password');

		$entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $id, 'token' => $token]);
    	
        if(!$user) {
        	$return = false;
        } else {
        	$from = $this->request->server->get('E_MAIL');

        	$email = (new TemplatedEmail())
                ->from($from)
                ->to($send_email)
                ->subject('Przypomnienie hasła do konta w oprogramowaniu PojSoft.')
                ->htmlTemplate('emails/soft/forgot.html.twig')
                ->context([
                    'user' => $send_email,
                    'password' => $password,
                ]);

            $sentEmail = $mailer->send($email);
            $messageId = $sentEmail->getMessageId();

            if($messageId > 0) {
        		$return = true;
        	} else {
        		$return = false;
        	}
        }

        return new JsonResponse($return);
    }

    /**
     * @Route("/api/mail/send/deadlines", name="api_mail_send_deadlines")
     */
    public function mailSendDeadlines(Request $request)
    {
    	$id = $request->query->get('id');
    	$token = $request->query->get('token');
    	$email = $request->query->get('email');
    	$vehicle_name = $request->query->get('vehicle_name'); // name
    	$vehicle_register = $request->query->get('vehicle_register'); // rejestracja
        $notify = $request->query->get('notify');    // jakie powiadomienie    
        $date = $request->query->get('date');          // data wydarzenia
        $remained = $request->query->get('remained'); // ile do końca   
        $from = $this->request->server->get('E_MAIL');

		$entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $id, 'token' => $token]);
    	
        if(!$user) {
        	$return = false;
        } else {

        	$text = $this->switchNotify($notify);

        	$email = (new TemplatedEmail())
                ->from($from)
                ->to($send_email)
                ->subject('Za '.$remained.' dni samochód '.$vehicle_name.' ('.$vehicle_register.') '.$text)
                ->htmlTemplate('emails/soft/deadline.html.twig')
                ->context([
                    'user' => $send_email,
                    'vehicle_name' => $vehicle_name,
                    'vehicle_register' => $vehicle_register,
                    'notify' => $notify,
                    'date' => $date,
                    'remained' => $remained,
                    'text' => $text,
                ]);

            $sentEmail = $mailer->send($email);
            $messageId = $sentEmail->getMessageId();

            if($messageId > 0) {
        		$return = true;
        	} else {
        		$return = false;
        	}
        }

        return new JsonResponse($return);
    }

    /**
     * @Route("/api/sms/send/deadlines", name="api_sms_send_time")
     */
    public function smsSendTime(Request $request)
    {
    	$id = $request->query->get('id');
    	$token = $request->query->get('token');
    	$phone = $request->query->get('phone');
    	$vehicle_name = $request->query->get('vehicle_name');
    	$vehicle_register = $request->query->get('vehicle_register');
        $notify = $request->query->get('notify');        
        $date = $request->query->get('date');              
        $remained = $request->query->get('remained');


		$entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $id, 'token' => $token]);
    	
        if(!$user) {
        	$return = false;
        } else {

        	$text = $this->switchNotify($notify);

        	$curl = curl_init();

			$url = $this->request->server->get('SMS_URL');
			$appkey = $this->request->server->get('SMS_APPKEY');
			$secret = $this->request->server->get('SMS_SECRET');

			$data = array(
			    'phone_number' => '+48'.$phone,
			    'sender_id' => 'Przypomnienie z PojSoft.',
			    'message' => 'Za '.$remained.' dni samochód '.$vehicle_name.' ('.$vehicle_register.') '.$text.' Dokładna data to: '.$date.'.'
			);

			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, "$appkey:$secret");
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

			$result = curl_exec($curl);

			if($result->data['status_desc'] == 'OK') {
        		$return = true;
        	} else {
        		$return = false;
        	}
        }

        return new JsonResponse($return);
    }

    public function switchNotify($notify) {
        switch($notify) {
        	case 'overview':
        		$text = "minie przegląd.";
        	break;
        	case 'oil':
        		$text = "minie termin wymiany oleju.";
        	break;
        	case 'udt':
        		$text = "minie przegląd UDT.";
        	break;
        	case 'documents':
        		$text = "dokumenty stracą ważność.";
        	break;
        	case 'oc':
        		$text = "minie termin ubezpieczenia.";
        	break;
        	case 'warranty':
        		$text = "skończy się gwarancja.";
        	break;
        	case 'mechanic':
        		$text = "odbędzie się wizyta u mechanika.";
        	break;
        	default:
        		$text = "wystąpi błąd Matrixa.";
        	break;
        }

        return $text;
    }

    /**
     * @Route("/api/help/send", name="api_help_send")
     */
    public function sendHelpApi(Request $request)
    {
    	$id = $request->query->get('id');
    	$token = $request->query->get('token');
    	$text = $request->query->get('text');

		$entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $id, 'token' => $token]);
    	
        if(empty($user)) {
        	$return = false;
        } else {

        	$help = new Help();
        	$help->setUser($user);
        	$help->setText($text);
        	$help->setDate(new \DateTime());
        	$help->setWhat(1);
        	$entityManager->persist($help);
        	$entityManager->flush();

        	if($help->getId() > 0) {
        		$return = true;
        	} else {
        		$return = false;
        	}
        }

        return new JsonResponse($return);
    }

    /**
     * @Route("/api/help/load", name="api_help_load")
     */
    public function loadHelpApi(Request $request)
    {
    	$id = $request->query->get('id');
    	$token = $request->query->get('token');

		$entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $id, 'token' => $token]);
    	
        if(!$user) {
        	$return = false;
        } else {
        	$help = $entityManager->getRepository(Help::class)->findBy(array('user' => $user));
        	$return = array();
        	foreach($help as $tab) {
        		$return[] = array('text' => $tab->getText(), 'what' => $tab->getWhat(), 'date' => $tab->getDate());
        	}
        }

        return new JsonResponse($return);
    }
}
