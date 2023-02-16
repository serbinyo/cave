<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Mercure\Discovery;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 *
 */
class ChatController extends AbstractController
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
     * @Route("/chat", name="chat")
     *
     */
    public function index(Request $request, Discovery $discovery, Authorization $authorization)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $discovery->addLink($request);
        $authorization->setCookie($request, [
            '*'
        ]);


        return $this->render('chat/index.html.twig');
    }
}
