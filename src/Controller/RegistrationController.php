<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\CodeGenerator;
use App\Service\Mailer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 *
 */
class RegistrationController extends AbstractController
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/registration", name="app_registration")
     *
     *
     * @param Request                     $request
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param Mailer                      $mailer
     *
     * @return RedirectResponse|Response
     *
     * @throws TransportExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordEncoder,
        Mailer $mailer
    ) {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the new users password
            $user->setPassword($passwordEncoder->hashPassword($user, $user->getPassword()));

            // Set their role
            $user->setRoles(['ROLE_USER']);

            $user->setConfirmationCode(CodeGenerator::getConfirmationCode());

            // Save
            $em = $this->doctrine->getManager();
            $em->persist($user);
            $em->flush();

            // Send confirmation email
            // Временно убираем подтверждение регистрации на email
            // $mailer->sendConfirmationMessage($user);
            // return $this->redirectToRoute('need_to_confirm');
            return $this->render('security/account_create.html.twig');
        }

        return $this->render(
            'registration/index.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/confirm", name="need_to_confirm")
     */
    public function waitForConfirmEmail()
    {
        return $this->render('security/wait_for_confirmation.html.twig');
    }

    /**
     * @Route("/confirm/{code}", name="email_confirmation")
     */
    public function confirmEmail(UserRepository $userRepository, string $code)
    {
        /** @var User $user */
        $user = $userRepository->findOneBy(['confirmationCode' => $code]);

        if ($user === null) {
            return new Response('404'); //todo вернуть нормальную 404 ошибку
        }

        $user->clearConfirmationCode();
        $user->setEnable(true);

        $em = $this->doctrine->getManager();

        $em->flush();

        return $this->render('security/account_confirm.html.twig', [
            'user' => $user,
        ]);
    }
}
