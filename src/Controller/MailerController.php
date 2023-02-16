<?php
/**
 * Created by PhpStorm
 * User: serbin
 * Date: 19.03.2022
 * Time: 14:57
 */


namespace App\Controller;

use App\Service\Mailer;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MailerController
 *
 * @package App\Controller
 */
class MailerController extends AbstractController
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Mailer $mailer
     */
    public function __construct(
        Mailer $mailer,
        LoggerInterface $logger
    ) {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * @throws Exception
     *
     * @Route("/test-mail", name="test-mail")
     */
    public function index()
    {
        $this->logger->info('Отправка тестового письма');

        try {
            $this->mailer->sendTestEmail();
        }catch (TransportExceptionInterface $e) {
            $this->logger->critical('test_mail_debug' . $e->getMessage());
        }

        return new Response(
            '<html><body>Отправка тестового письма.</body></html>'
        );
    }
}
