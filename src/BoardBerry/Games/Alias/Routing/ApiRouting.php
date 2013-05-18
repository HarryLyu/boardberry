<?php
namespace BoardBerry\Games\Alias\Routing;

use BoardBerry\Games\Alias\Game\Events\RoomEventManager;
use BoardBerry\Games\Alias\Game\GameLogic;
use BoardBerry\Games\Alias\Game\Room\RoomManager;
use BoardBerry\Games\Alias\Game\Room\RoomResponseFormatter;
use BoardBerry\Games\Alias\Game\Room\Words\WordManager;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRouting implements ControllerProviderInterface
{
    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $collection = $app['controllers_factory'];

        $collection->match('room', function (Request $request) use ($app) {
            /** @var RoomManager $roomManager */
            $roomManager = $app['alias.room-manager'];

            if (!$action = $request->get('action')) {
                throw new \Exception('No action passed');
            };

            switch ($action) {
                case 'create':
                    if (($ownerId = $request->get('owner')) === null) {
                        throw new \Exception('No owner passed');
                    };

                    $room = $roomManager->createRoom($ownerId);

                    $roomEventManager = new RoomEventManager($app['comet'], $room->roomId);
                    $game = new GameLogic($roomEventManager, $room);
                    $game->init($ownerId);

                    $formatter = new RoomResponseFormatter($roomEventManager, $room);

                    return new JsonResponse($formatter->format());
            }
        });

        $collection->match('room/{roomId}', function (Request $request, $roomId) use ($app) {
            /** @var RoomManager $roomManager */
            $roomManager = $app['alias.room-manager'];

            $roomEventManager = new RoomEventManager($app['comet'], $roomId);

            if (!$action = $request->get('action')) {
                throw new \Exception('No action passed');
            };

            switch ($action) {
                case 'join-room':
                    if (($playerId = $request->get('user')) === null) {
                        throw new \Exception('No player passed');
                    };

                    $room = $roomManager->getRoom($roomId);
                    $game = new GameLogic($roomEventManager, $room);
                    $game->addPlayer($playerId);

                    $formatter = new RoomResponseFormatter($roomEventManager, $room);

                    return new JsonResponse($formatter->format());

                case 'join-team':
                    if (($playerId = $request->get('user')) === null) {
                        throw new \Exception('No player passed');
                    };

                    if (($teamId = $request->get('team')) === null) {
                        throw new \Exception('No team passed');
                    };

                    $room = $roomManager->getRoom($roomId);
                    $game = new GameLogic($roomEventManager, $room);
                    $game->addPlayerToTeam($teamId, $playerId);

                    return new JsonResponse(['result' => 'ok']);

                case 'add-team':
                    if (($playerId = $request->get('user')) === null) {
                        throw new \Exception('No player passed');
                    };

                    $room = $roomManager->getRoom($roomId);
                    $game = new GameLogic($roomEventManager, $room);
                    $game->addTeam($playerId);

                    return new JsonResponse(['result' => 'ok']);

                case 'start-game':
                    $room = $roomManager->getRoom($roomId);
                    $game = new GameLogic($roomEventManager, $room);
                    $game->startGame(new WordManager());

                    return new JsonResponse(['result' => 'ok']);

                case 'start-explanation':

                    $room = $roomManager->getRoom($roomId);
                    $game = new GameLogic($roomEventManager, $room);
                    $game->startExplanation();

                    return new JsonResponse(['result' => 'ok']);


                case 'finish-explanation':

                    if (($tempResult = $request->get('words')) === null) {
                        throw new \Exception('No words passed');
                    };

                    $room = $roomManager->getRoom($roomId);
                    $game = new GameLogic($roomEventManager, $room);
                    $game->finishExplanation($tempResult);

                    return new JsonResponse(['result' => 'ok']);

                case 'edit-result':

                    if (($wordId = $request->get('wordId')) === null) {
                        throw new \Exception('No words passed');
                    };

                    $room = $roomManager->getRoom($roomId);
                    $game = new GameLogic($roomEventManager, $room);
                    $game->editResult($wordId);

                    return new JsonResponse(['result' => 'ok']);

                case 'save-results':

                    $room = $roomManager->getRoom($roomId);
                    $game = new GameLogic($roomEventManager, $room);
                    $game->addScore();

                    return new JsonResponse(['result' => 'ok']);
            }
        });

        return $collection;
    }
}