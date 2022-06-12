<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Yaml\Yaml;

// klasy
use App\Entity\User;
use App\Entity\UserNotify;
use App\Entity\News;


class UserController extends AbstractController
{

    /**
     * @Route("/user/dashboard", name="dashboard")
     */
    public function indexDashboard(Request $request, UserInterface $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user_notify = $entityManager->getRepository(UserNotify::class)->findBy(array('user' => $user), array('date' => 'DESC'), 30, 0);

        $news = $entityManager->getRepository(News::class)->findBy(array(), array('date' => 'DESC'), 3, 0);

        return $this->render('user/dashboard.html.twig', ['notify' => $user_notify, 'news' => $news]);
    }

    /**
     * @Route("/user/settings", name="settings")
     */
    public function indexSettings(Request $request)
    {
        $user = $this->getUser();

        return $this->render('user/settings.html.twig', ['user' => $user]);
    }

}
