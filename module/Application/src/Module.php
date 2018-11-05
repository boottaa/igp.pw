<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Model\Links;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
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
                Controller\IndexController::class => function ($container) {
                    /**
                     * @var  ContainerInterface $container
                     */
//                    $adapter = $container->get(AdapterInterface::class);
//                    $sql = new Sql($adapter);
//                    return new Controller\IndexController($sql);

                    $adapter = $container->get(AdapterInterface::class);
                    $logger = $container->get(Logger::class);
                    $isDebug = ($container->get('config'))['isDebug'];

                    $model = new Links($adapter, $logger, $isDebug);

                    return new Controller\IndexController($model);
                },
                Controller\AdminController::class => function ($container) {
                    /**
                     * @var  ContainerInterface $container
                     */
                    $adapter = $container->get(AdapterInterface::class);
                    $sql = new Sql($adapter);
                    return new Controller\AdminController($sql);
                },

                Controller\IgpController::class => function ($container) {
                    /**
                     * @var  ContainerInterface $container
                     */
                    $adapter = $container->get(AdapterInterface::class);
                    $sql = new Sql($adapter);
                    return new Controller\IgpController($sql);
                }
            ]
        ];
    }
}
