<?php

declare(strict_types=1);

namespace App\Controller\Personal;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class PersonalController extends AbstractController
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
     * @return Response
     */
    #[Route('/profile', name: 'profile')]
    public function index(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class)
            ->add(
                'name',
                TextType::class,
                ['label' => 'Имя пользователя']
            )
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save
            $em = $this->doctrine->getManager();
            $em->persist($user);
            $em->flush();
        }

        $response = $this->render('profile/account.html.twig',
            [
                'form' => $form->createView(),
            ]
        );

        return $response;
    }
}
