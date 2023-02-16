<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Serializer\Deserializer;
use App\Service\Serializer\Wim\BreathingExerciseDeserializer;
use App\UseCase\Wim\FillUserExercises\Command;
use App\UseCase\Wim\FillUserExercises;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    #[Route('/')]
    public function index(FillUserExercises\Handler $getExercisesService, Deserializer $deserializer)
    {
        $user = $this->getUser();
        $userExercises = [];

        if ($user !== null) {
            $getExercisesService->handle(
                new Command($user)
            );

            $userExercises = BreathingExerciseDeserializer::deserializeCollection(
                $user->getBreathingExercises(),
                'array'
            );
        }

        $response =  $this->render('index/index.html.twig',
            [
                'userExercises' => $userExercises
            ]
        );

        return $response;
    }
}
