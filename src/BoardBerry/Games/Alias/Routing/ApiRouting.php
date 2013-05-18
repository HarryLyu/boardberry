<?php
namespace BoardBerry\Games\Alias\Routing;

use BoardBerry\Games\Alias\Game;
use BoardBerry\Games\Alias\Room\RoomManager;
use BoardBerry\Games\Alias\Room\RoomResponseFormatter;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRouting implements ControllerProviderInterface {
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

        $collection->match('room', function (Request $request) use ($app)
        {
            /** @var RoomManager $roomManager */
            $roomManager = $app['alias.room-manager'];

            if (!$action = $request->get('action')){
                throw new \Exception('No action');
            };

            switch ($action) {
                case 'create':
                    if (!$ownerId = $request->get('owner')){
                        throw new \Exception('No owner');
                    };

                    $room = $roomManager->createRoom($ownerId);

                    $game = new Game($room, new RoomResponseFormatter());
                    $game->init($ownerId);

                    return new JsonResponse($game->getResponseFormatter()->format());
            }
        });

        $collection->match('room/{id}', function ($id)
        {
            return 'room id ' . $id;
        });

        return $collection;
    }
}