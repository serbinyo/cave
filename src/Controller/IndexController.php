<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 *
 */
class IndexController extends AbstractController
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
    #[Route('/', name: 'homepage')]
    public function index()
    {
        $response =  $this->render('homepage/index.html.twig');

        return $response;
    }
}
