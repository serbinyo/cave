<?php

declare(strict_types=1);
/**
 * Created by PhpStorm
 * User: serbin
 * Date: 01.08.2022
 * Time: 19:51
 */


namespace App\Controller\Wim;


use App\DTO\Wim\LapDTO;
use App\Service\Serializer\Deserializer;
use App\UseCase\Wim\AddExercise\Command;
use App\UseCase\Wim\AddExercise\Handler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class BreathingExercise
 *
 * @package App\Controller\Wim
 */
class BreathingExercise extends AbstractController
{
    private Deserializer $deserializer;

    public function __construct(Deserializer $deserializer)
    {
        $this->deserializer = $deserializer;
    }


    /**
     *
     * @return JsonResponse
     * @throws \Doctrine\DBAL\Exception
     */
    #[Route('/breath/add', name: 'addBreathExercise', methods: ['POST'])]
    public function add(Request $request, Handler $handler, UserInterface $user)
    {
        #1. Инициализируем DTO
        $laps = $this->deserializer->jsonCollectionToObjectCollection(
            $request->request->get('laps'),
            LapDTO::class
        );

        #2. Инициализируем команду
        $command = new Command($user, $laps);

        #3. передаем команду в сценарий. Выполняем операцию.
        $newExercise = $handler->handle($command);

        return $this->json(['result' => true], Response::HTTP_CREATED, [], []);
    }

}