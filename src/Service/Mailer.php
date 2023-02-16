<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Mailer
 *
 * @package App\Service
 */
class Mailer
{
    /**
     *
     */
    public const FROM_ADDRESS = 'serbinyo@yandex.ru';

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(
        MailerInterface $mailer,
        Environment $twig

    )  {
        $this->mailer = $mailer;
        $this->twig = $twig;

    }

    /**
     *
     * @throws TransportExceptionInterface
     */
    public function sendTestEmail()
    {
        $email = (new Email())
            //->from(self::FROM_ADDRESS)
            ->from(new Address(self::FROM_ADDRESS, 'Алексис Сербин'))
            ->to('serbinyo@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Тестовое сообщение разработки!')
            ->html('<h2>Это тестовое сообщение разработки</h2>');

        $this->mailer->send($email);
    }


    /**
     * @param User $user
     *
     * @throws TransportExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendConfirmationMessage(User $user)
    {
        $messageBody = $this->twig->render('security/confirmation.html.twig', [
            'user' => $user
        ]);

        $email = (new Email())
            ->from(new Address(self::FROM_ADDRESS, 'Александр Сербин'))
            ->to($user->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Вы успешно прошли регистрацию!')
            ->html($messageBody);

        $this->mailer->send($email);
    }
}