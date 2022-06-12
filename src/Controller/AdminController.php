<?php

namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\EventDispatcher\GenericEvent;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Dompdf\Dompdf;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;

// klasy
use App\Entity\User;
use App\Entity\Services;
use App\Entity\UserInvoices;
use App\Entity\InvoicesTable;
use App\Entity\UserDiscounts;
use App\Entity\Partners;
use App\Entity\UserNotify;
use App\Entity\UserServices;
use App\Form\RegisterType;
use App\Entity\PartnersHistory;
use App\Entity\Discounts;


class AdminController extends EasyAdminController
{
    // dodanie notice
    public function addNote($text, $request) {
        $entityManager = $this->getDoctrine()->getManager();
        $ip = $request->getClientIp();

        $note = new UserNotify();
        $note->setUser($this->getUser());
        $note->setDate(new \DateTime());
        $note->setIp($ip);
        $note->setText($text);
        $entityManager->persist($note);
        $entityManager->flush();
    }

    /**
     * @Route("/user", name="easyadmin")
     */
    public function indexAction(Request $request)
    {
        return parent::indexAction($request);
    }

    // zapytanie dla list, które wyświetla tylko user = user
    protected function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null) {

        if($this->entity['name'] == 'UserServices' || $this->entity['name'] == 'UserInvoices' || $this->entity['name'] == 'UserNotify') {

            $dqlFilter = "entity.user = ".$this->getUser()->getID()."";
        }

        return parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);
    }

    /**
     * @Route("/user/services", name="services")
     */
    public function indexServices(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $services = $entityManager->getRepository(Services::class)->findAll();

        return $this->render('user/services.html.twig', ['services' => $services]);
    }

    /**
     * @Route("/user/invoice/pdf/{id}", name="invoice_pdf")
     */
    public function showInvoicePdf($id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $invoice = $entityManager->getRepository(UserInvoices::class)->findOneBy(array('id' => $id, 'user' => $this->getUser()));

        $path = "Faktura_DymCode_".$invoice->getName().".pdf";
        $pdf = $this->render('user/invoice_pdf.html.twig', ['entity' => $invoice]);
  
        // to pdf
        $dompdf = new Dompdf('UTF-8');
        $dompdf->loadHtml($pdf->getContent());
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream($path);
    }

    /**
     * @Route("/user/support", name="support")
     */
    public function indexSupport(Request $request)
    {
        return $this->render('user/support.html.twig');
    }

    /**
     * @Route("/user/support/send", name="send_support")
     */
    public function sendSupport(Request $request, MailerInterface $mailer)
    {
        $form = $request->request->get('contact');
        $user = $this->getUser();

        $email = (new TemplatedEmail())
            ->from($form['service'])
            ->to($user->getEmail())
            ->subject($form['subject'])
            ->htmlTemplate('emails/contact.html.twig')
            ->context([
                'user' => $user,
                'form' => $form,
            ]);

        $sentEmail = $mailer->send($email);
        $messageId = $sentEmail->getMessageId();

        if($messageId > 0) {
            $this->addFlash('success', 'Poprawnie wysłano wiadomość. Niedługo nadejdzie odpowiedź.');
        } else {
            $this->addFlash('danger', 'Niestety, ale wystąpił błąd. Prosimy o inną formę kontaktu.');
        }

        return $this->redirectToRoute('support');
    }

    /**
     * @Route("/user/partner", name="partner")
     */
    public function indexPartner(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $partner = $entityManager->getRepository(Partners::class)->findOneBy(array('user' => $this->getUser()));

        return $this->render('user/partner.html.twig', array('partner' => $partner));
    }

    /**
     * @Route("/user/partner/add", name="add_partner")
     */
    public function addPartner(Request $request)
    {
        $form = $request->request->get('code');

        $entityManager = $this->getDoctrine()->getManager();
        $code = $entityManager->getRepository(Partners::class)->findOneBy(array('code' => $form['code']));

        if($code) {
            $this->addFlash('danger', 'Już posiadamy partnera o tym kodzie. Spróbuj z innym kodem.');
            return $this->redirectToRoute('partner'); 
        } else {
            $partner = new Partners();
            $partner->setCode($form['code']);
            $partner->setPercent(10);
            $partner->setAmount(0);
            $partner->setUser($this->getUser());
            $entityManager->persist($partner);
            $entityManager->flush();

            $this->addNote('Podjęto się partnerstwa.', $request);
            $this->addFlash('success', 'Witaj w gronie partnerów.');
            return $this->redirectToRoute('partner');
        }
    }

    /**
     * @Route("/user/discount/use/{id}", name="use_discount")
     */
    public function useDiscount($id, Request $request)
    {
        $form = $request->request->get('code');
        $entityManager = $this->getDoctrine()->getManager();
        $code = $entityManager->getRepository(Discounts::class)->findOneBy(array('code' => $form['text'], 'term' => Carbon::now()));

        if(!empty($code)) {
            $user_code = $entityManager->getRepository(UserDiscounts::class)->findOneBy(array('user' => $this->getUser(), 'discounts' => $code->getId()));

            if(empty($user_code)) {
                $invoice = $entityManager->getRepository(UserInvoices::class)->find($id);

                $text = "SELECT SUM(i.cost) AS sum_cost, SUM(i.money) AS sum_money FROM App\Entity\InvoicesTable i WHERE i.id = :id";
                $table = $entityManager->createQuery($text)
                    ->setParameter('id', $id);
                $table = $table->getSingleResult();

                $money = $table['sum_money'] * $code->getDiscount() / 100;
                $cost = $table['sum_cost'] * $code->getDiscount() / 100;

                $invoice_table = new InvoicesTable();
                $invoice_table->setName("Rabat w wysokości ".$code->getDiscount()." %.");
                $invoice_table->setCost('-'.$cost);
                $invoice_table->setLot(1);
                $invoice_table->setTax(23);
                $invoice_table->setMoney('-'.$money);
                $invoice_table->setInvoice($invoice);
                $entityManager->persist($invoice_table);

                $user_discount = new UserDiscounts();
                $user_discount->setMoney($money);
                $user_discount->setDate(Carbon::now());
                $user_discount->setUser($this->getUser());
                $user_discount->setInvoice($invoice);
                $user_discount->setDiscounts($code);
                $entityManager->persist($user_discount);

                $invoice->setDisc($code);
                $entityManager->flush();

                $this->addNote('Użyto kodu rabatowego dla faktury.', $request);
                $this->addFlash('success', 'Poprawnie użyto kodu rabatowego.');
            } else {
                $this->addFlash('danger', 'Ten kod został już wykorzystany.');
            }
        } else {
            $this->addFlash('danger', 'Nie ma takiego kodu lub jest już nieaktualny.');
        }

        return $this->redirectToRoute('easyadmin', array(
            'action' => 'show',
            'id' => $id,
            'entity' => 'UserInvoices',
        ));
    }

    // wyświetlenie faktury user
    protected function showUserInvoicesAction() {
        $id = $this->request->query->get('id');

        $entityManager = $this->getDoctrine()->getManager();
        $record = $entityManager->getRepository(UserInvoices::class)->findOneBy(array('id' => $id, 'user' => $this->getUser()));
        $payPath = $this->payPath($record);

        return $this->render('user/show_invoice.html.twig', ['entity' => $record, 'pay_path' => $payPath]);
    }

    // wyświetlenie faktury admin
    protected function showUserInvoicesAdminAction() {
        $id = $this->request->query->get('id');

        $entityManager = $this->getDoctrine()->getManager();
        $record = $entityManager->getRepository(UserInvoices::class)->find($id);
        $payPath = $this->payPath($record);

        return $this->render('user/show_invoice.html.twig', ['entity' => $record, 'pay_path' => $payPath]);
    }

    // generowanie płatności (link)
    public function payPath($invoice)
    {
        $url = $this->request->server->get('DOTPAY_BASE_URL');
        $id_shop = $this->request->server->get('DOTPAY_SHOP_ID');

        $money = 0;
        foreach($invoice->getInvoicesTable() as $tab) {
            $money = $money + $tab->getMoney();
        }

        $url_return = 'http://127.0.0.1:8001/admin/?entity=UserInvoices&action=list';
        $urlc = 'http://127.0.0.1:8001/invoice/pay/ok';

        return $url.'?id='.$id_shop.'&amount='.$money.'&currency=PLN&description=Zapłata za fakturę VAT: '.$invoice->getName().'&buttontext=Powrót do panelu DymCode&control='.$invoice->getId().'&firstname='.$invoice->getUser()->getFirstname().'&lastname='.$invoice->getUser()->getName().'&email='.$invoice->getUser()->getEmail().'&url='.$url_return.'&urlc='.$urlc.'&type=0';

    }

    /**
     * @Route("/invoice/pay/ok", name="pay_invoice")
     */
    public function payInvoice(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();


        // if & id z request od dotpay
        $id = 24; // dla testów
        $invoice = $entityManager->getRepository(UserInvoices::class)->find($id);
        $invoice->setDatePayment(Carbon::now());
        $invoice->setStatus(4);

        foreach($invoice->getInvoicesTable() as $tab) {
            $data = json_decode($tab->getData());

            if(!empty($data)) {
                $service = $entityManager->getRepository(UserServices::class)->find($data->id_user_service);
                $end_pack = Carbon::parse($service->getDateEndPack());
                $end_host = Carbon::parse($service->getDateDeleteHost());
                $end_pack->addMonth($data->time);
                $end_host->addMonth($data->time);

                $switch = $this->switcherService($data->service, $data->pack);

                $service->setPack($switch['id_pack']);
                $service->setDateEndPack($end_pack);
                $service->setDateDeleteHost($end_host);
                $entityManager->persist($service);


                // przeniesienie plików do hosta i przedłużenie softu w pliku oprogramowania
            }
        }

        $entityManager->flush();

        // prowizja od sprzedaży dla partnera
        $partner = $entityManager->getRepository(Partners::class)->findOneBy(array('code' => $invoice->getUser()->getCodePartner()));
        if($partner) {

            $money = 0;
            foreach($invoice->getInvoicesTable() as $tab) {
                $money = $money + $tab->getMoney();
            }

            $provision = ($money * $partner->getPercent()) / 100;

            $partner->setAmount($partner->getAmount() + $provision);

            $history = new PartnersHistory();
            $history->setMoney($provision);
            $history->setAmount($partner->getAmount());
            $history->setDate(new \DateTime());
            $history->setPartner($partner);
            $history->setStatus(2);
            $entityManager->persist($history);

            $entityManager->flush();
        }
        // koniec prowizji

        return new Response('OK');
    }

    /**
     * @Route("/user/order/{what}/{pack}/{gratis}/{extends}", name="order_service")
     */
    public function orderService($what, $pack, $gratis, $extends, Request $request)
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $entityManager = $this->getDoctrine()->getManager();
        $services = $entityManager->getRepository(Services::class)->findAll();

        if(!empty($this->getUser())) {
            if($extends > 0) {
                $user_service = $entityManager->getRepository(UserServices::class)->findOneBy(array('id' => $extends, 'user' => $this->getUser()));
                if(!empty($user_service)) {
                    $host = $user_service->getHost();
                } else {
                    $host = "";
                }
            } else {
                $host = "";
            }
        } else {
            $host = "";
        }

        return $this->render('start/new_service.html.twig', array('what' => $what, 'pack' => $pack, 'gratis' => $gratis, 'extends' => $extends, 'host' => $host, 'form' => $form->createView(), 'services' => $services));
    }

    /**
     * @Route("/user/order/{what}/{pack}/{gratis}/{extends}/send", name="order_send")
     */
    public function orderSend($what, $pack, $gratis, $extends, Request $request, KernelInterface $kernel)
    {
        $user = $this->getUser();
        $form = $request->request->get('order');
        $entityManager = $this->getDoctrine()->getManager();

        $user_service = $entityManager->getRepository(UserServices::class)->findOneBy(array('host' => $form['service_name']));

        if(!empty($user_service) && $user_service->getUser() == $this->getUser()) { // przedłuża pakiet

            $this->generateOrder($user, $user_service, $form, $gratis = 0, $extends, $entityManager);

            $this->addFlash('success', 'Poprawnie złożono zamówienie. Opłać fakturę, aby przedłużyć usługę.');
        } elseif(!empty($user_service)) {   // error, bo jest
            $this->addFlash('danger', 'Niestety, ale już ktoś posiada usługę o tej nazwie własnej.');

            return $this->redirectToRoute('order_service', array(
                'what' => $what,
                'pack' => $pack,
                'gratis' => $gratis,
                'extends' => $extends
            ));
        } elseif(empty($user_service)){
            $this->generateOrder($user, $user_service, $form, $gratis, $extends, $entityManager);

            // zlecenie instalacji skryptu.
            $application = new Application($kernel);
            $application->setAutoExit(false);

            $input = new ArrayInput([
                'command' => 'app:instal-pojsoft',
                'name' => $form['service_name'],
                'user' => $this->getUser()->getId(),
            ]);

            $output = new BufferedOutput();
            $application->run($input, $output);
            // koniec instalacji

            $this->addFlash('success', 'Poprawnie złożono zamówienie. Rozpoczęto instalację usługi. Zostaniesz powiadomiony o jej zakończeniu.');      
        } else {
            $this->addFlash('danger', 'Źle wypełniono formularz lub wystąpił problem z systemem zamówień.');

            return $this->redirectToRoute('order_service', array(
                'what' => $what,
                'pack' => $pack,
                'gratis' => $gratis,
                'extends' => $extends
            ));          
        }

        return $this->redirectToRoute('easyadmin', array(
            'action' => 'list',
            'entity' => 'UserInvoices',
        ));
    }

    public function generateOrder($user, $user_service, $form, $gratis, $extends, $entityManager) 
    {
        $date_payment = Carbon::now();
        if($gratis == 1) {
            $date_payment->addMonth(1);
        } else {
            $date_payment->addDay(14);
        }

        switch($form['pack']) {
            case 'MINI': $amount = 12; $id_pack = 1; break;
            case 'MEDIUM': $amount = 16; $id_pack = 2; break;
            case 'MAXI': $amount = 20; $id_pack = 3; break;
            case 'PRO': $amount = 25; $id_pack = 4; break;
            default: $amount = 0; $id_pack = 0; break;
        }

        $name = "Usługa ".$form['service'].". Pakiet: ".$form['pack'].".";

        $service_base = $entityManager->getRepository(Services::class)->findOneBy(array('name' => $form['service']));

        if($form['time'] == 1) {
            $amount = $amount + 1;
        } else {
            $amount = $amount;
        }

        if($gratis == 1) {
            $end_pack = Carbon::now()->addMonth(1);
            $end_host = Carbon::now()->addMonth(2);
        } else {
            $end_pack = Carbon::now()->addDay(14);
            $end_host = Carbon::now()->addMonth(1);
        }

        $data = ($amount*23)/123;
        $amount_bez = $amount - $data;
        $suma = $amount_bez*$form['time'];
        $vat = ($suma*23)/100;
        $sumvat = $suma + $vat;

        $client = "<strong>".$user->getNameCompany()."</strong><br/>".$user->getStreet()."<br/>".$user->getCodePost()." ".$user->getCity()."";

        if($extends == 0) {
            $service = new UserServices();
            $service->setUser($this->getUser());
            $service->setMyServices($service_base);
            $service->setPack($id_pack);
            $service->setHost($form['service_name']);
            $service->setDateStartPack(Carbon::now());
            $service->setDateEndPack($end_pack);
            $service->setDateDeleteHost($end_host);
            $entityManager->persist($service); 

            $entityManager->flush();
        } else {
            $service = $entityManager->getRepository(UserServices::class)->findOneBy(array('user' => $this->getUser(), 'host' => $form['service_name']));
        }

        $data = array('service' => $form['service'], 'pack' => $form['pack'], 'gratis' => $gratis, 'extends' => $extends, 'time' => $form['time'], 'id_user_service' => $service->getId(), 'host' => $form['service_name']);

        $invoice = new UserInvoices();
        $invoice->setUser($this->getUser());
        $invoice->setAddressCompany($client);
        $invoice->setAddressMy('<strong>Admin, Inc.</strong><br>795 Folsom Ave, Suite 600<br>San Francisco, CA 94107<br>Phone: (804) 123-5432<br>Email: info@almasaeedstudio.com');
        $invoice->setStatus(0);
        $invoice->setName('test/2020');
        $invoice->setDateIssued(new \DateTime());
        $invoice->setDatePayment($date_payment);
        $entityManager->persist($invoice);

        $invoice_table = new InvoicesTable();
        $invoice_table->setName($name);
        $invoice_table->setCost($suma);
        $invoice_table->setLot(1);
        $invoice_table->setTax(23);
        $invoice_table->setMoney($sumvat);
        $invoice_table->setInvoice($invoice);
        $invoice_table->setData(json_encode($data));
        $entityManager->persist($invoice_table);

        $entityManager->flush();
    }

    /**
     * @Route("/partner/{partner}/{url}", name="partner_path")
     */
    public function pathPartner($partner, $url)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $base_partner = $entityManager->getRepository(Partners::class)->findOneBy(array('code' => $partner));

        if($base_partner) {
            $cookie = new Cookie(
                    'dc_partner',    // Cookie name.
                    $partner,   // Cookie value.
                    time() + ( 2 * 365 * 24 * 60 * 60)  // Expires 2 years.
            );

            $res = new Response();
            $res->headers->setCookie($cookie);
            $res->send();

            $history = new PartnersHistory();
            $history->setMoney(0);
            $history->setAmount($base_partner->getAmount());
            $history->setDate(new \DateTime());
            $history->setPartner($base_partner);
            $history->setStatus(0);
            $entityManager->persist($history);

            $entityManager->flush();
        }

        return $this->redirect('https://www.'.$url);
    }

}
