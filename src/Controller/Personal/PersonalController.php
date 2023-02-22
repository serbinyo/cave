<?php

declare(strict_types=1);

namespace App\Controller\Personal;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\File;

/**
 *
 */
class PersonalController extends AbstractController
{
    private ManagerRegistry $doctrine;

    private KernelInterface $appKernel;

    public function __construct(ManagerRegistry $doctrine, KernelInterface $appKernel)
    {
        $this->doctrine = $doctrine;
        $this->appKernel = $appKernel;
    }

    /**
     * @return Response
     */
    #[Route('/profile', name: 'profile')]
    public function index(Request $request, SluggerInterface $slugger)
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createFormBuilder($user)
            ->add('email',
                EmailType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'label'    => 'Имя пользователя',
                    'required' => false,
                ]
            )
            ->add(
                'avatar',
                FileType::class,
                [
                    'label'       => false,
                    // unmapped means that this field is not associated to any entity property
                    'mapped'      => false,

                    // make it optional so you don't have to re-upload the PDF file
                    // every time you edit the Product details
                    'required'    => false,

                    // unmapped fields can't define their validation using annotations
                    // in the associated entity, so you can use the PHP constraint classes
                    'constraints' => [
                        new File([
                            'maxSize' => '800k',
                            //'mimeTypes' => [
                            //    'application/pdf',
                            //    'application/x-pdf',
                            //],
                            //'mimeTypesMessage' => 'Please upload a valid PDF document',
                            //accept="image/png, image/jpeg"
                        ])
                    ]
                ]
            )
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save
            $projectRoot = $this->appKernel->getProjectDir();

            /** @var UploadedFile $brochureFile */
            $avatarFile = $form->get('avatar')->getData();

            // this condition is needed because the 'avatar' field is not required
            // so the file must be processed only when a file is uploaded
            if ($avatarFile) {
                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . $user->getId() . '.' . $avatarFile->guessExtension();

                // Move the file to the directory where brochures are stored

                try {
                    $avatarFile->move(
                        $projectRoot . $this->getParameter('avatar_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    throw new FileException($e->getMessage());
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setAvatar($newFilename);
            }

            // Save
            $em = $this->doctrine->getManager();
            $em->persist($user);
            $em->flush();
        }

        $response = $this->render('profile/account.html.twig',
            [
                'form'        => $form->createView(),
            ]
        );

        return $response;
    }
}
