<?php

declare(strict_types=1);

namespace App\Controller\Personal;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 *
 */
class PersonalController extends AbstractController
{
    private Environment $twig;

    /**
     * ConferenceController constructor.
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @return Response
     */
    #[Route('/profile', name: 'profile')]
    public function index()
    {
        $response = $this->render('profile/account.html.twig');

        return $response;
    }
}
