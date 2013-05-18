<?php
namespace BoardBerry\Common\Routing;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CommonApiRouting implements ControllerProviderInterface
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
        /** @var ControllerCollection $collection */
        $collection = $app['controllers_factory'];

        $collection->match('/user', function (Request $request) use ($app) {
                $auth = $request->get('auth');
                $user = $request->get('user');

                if(isset($user['error'])) {
                    throw new \Exception('error fb');
                }

                $userData = $app['user']->register($auth, $user);
                if($userData) {
                    $result = [
                        'result'=>'ok',
                        'user'=>$userData
                    ];
                }   else    {
                    $result = [
                        'result'=>'user reg failed'
                    ];
                }
                return new JsonResponse($result);
        });


        $collection->match('/user/{id}', function (Request $request, $id) use ($app) {

                $userID = filter_var($id, FILTER_VALIDATE_INT);
                if(!$userID) {
                    throw new \Exception('failed uid');
                }

                $user = $app['user']->get($userID);

                if($user) {
                    $result = [
                        'result'=>'ok',
                        'user'=>$user
                    ];
                }   else {
                    $result = [
                        'result'=>'user didn\'t found'
                    ];
                }
                return new JsonResponse($result);
        });

        return $collection;
    }
}