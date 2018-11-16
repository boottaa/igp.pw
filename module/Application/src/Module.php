<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Model\FollowLinks;
use Application\Model\Links;
use Auth\Helpers\Session;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Zend\Log\Logger;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Navigation\Navigation;

class Module implements ConfigProviderInterface
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                Session::class => function( $container )
                {
                    /**
                     * @var  ContainerInterface $container
                     */
                    $adapter = $container->get(AdapterInterface::class);
                    return new Session(new TableGateway('sessions', $adapter));
                }
            ]
        ];
    }

    public function getControllerConfig()
    {
        return [
            'initializers' => [
                function ( $container ) {
                    /**
                     * @var  ContainerInterface $container
                     * @var Navigation $navigation
                     * @var Session $session
                     */
                    $session = ($container->get(Session::class));
                    if ($session->checkSession()) {
                        $navigation = $container->get(Navigation::class);
                        $navigation->findOneBy('label', 'Login')->set('visible', false);
                        $navigation->findOneBy('label', 'Admin')->set('visible', true);
                        $navigation->findOneBy('label', 'Logout')->set('visible', true);
                    }
                }
            ],

            'factories' => [
                Controller\IndexController::class => function ($container) {
                    /**
                     * @var  ContainerInterface $container
                     */

                    $adapter = $container->get(AdapterInterface::class);
                    $logger = $container->get(Logger::class);
                    $isDebug = ($container->get('config'))['isDebug'];

                    $modelLinks = new Links($adapter, $logger, $isDebug);
                    $modelFollowLinks = new FollowLinks($adapter, $logger, $isDebug);
                    $session = ($container->get(Session::class));

                    return new Controller\IndexController($modelLinks, $session);
                },
                Controller\AdminController::class => function ($container) {
                    /**
                     * @var  ContainerInterface $container
                     */
                    $adapter = $container->get(AdapterInterface::class);
                    $logger = $container->get(Logger::class);
                    $isDebug = ($container->get('config'))['isDebug'];

                    $session = ($container->get(Session::class));
                    $followLinks = new Links($adapter, $logger, $isDebug);

                    return new Controller\AdminController($followLinks, $session);
                },

                Controller\IgpController::class => function ($container) {
                    /**
                     * @var  ContainerInterface $container
                     * @var Navigation $navigation
                     */
                    
                    $adapter = $container->get(AdapterInterface::class);
                    $logger = $container->get(Logger::class);
                    $isDebug = ($container->get('config'))['isDebug'];

                    $model = new Links($adapter, $logger, $isDebug);

                    return new Controller\IgpController($model);
                }
            ]
        ];
    }
}
