<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Auth;

use Application\Model\Links;
use Application\Model\Users;
use Auth\Helpers\Session;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Log\Logger;
use Zend\ModuleManager\Feature\ConfigProviderInterface;


class Module implements ConfigProviderInterface
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getControllerConfig()
    {
        

        return [
            'factories' => [
                Controller\AuthController::class => function ($container) {
                    /**
                     * @var  ContainerInterface $container
                     */

                    $adapter = $container->get(AdapterInterface::class);
                    $logger = $container->get(Logger::class);
                    $isDebug = ($container->get('config'))['isDebug'];

                    $model = new Users($adapter, $logger, $isDebug);
                    $session = new Session(new TableGateway('sessions', $adapter));

                    return new Controller\AuthController( $model, $session );
                },
            ]
        ];
    }
}
