<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Debug for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Debug;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\EventManager\Event;
use Zend\Navigation\Page\Mvc;
use Zend\View\Model\ViewModel;

class Module implements AutoloaderProviderInterface
{

    public function init(ModuleManager $moduleManager)
    {
        $eventManager = $moduleManager->getEventManager();
        $eventManager->attach('loadModules.post', array(
            $this,
            'loadedModulesInfo'
        ));
    }

    public function loadedModulesInfo(Event $event)
    {
        $moduleManager = $event->getTarget();
        $loadedModules = $moduleManager->getLoadedModules();
        error_log(var_export($loadedModules, true));
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/', __NAMESPACE__)
                )
            )
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach('dispatch.error', array(
            $this,
            'handleError'
        ));
        
        // Below is how we get access to the service manager
        $serviceManager = $e->getApplication()->getServiceManager();
        // Here we start the timer
        $timer = $serviceManager->get('timer');
        $timer->start('mvc-execution');
        
        // And here we attach a listener to the finish event that has to be executed with priority 2
        // The priory here is 2 because listeners with that priority will be executed just before the
        // actual finish event is triggered.
        $eventManager->attach(MvcEvent::EVENT_FINISH, array(
            $this,
            'getMvcDuration'
        ), 2);
        
        $eventManager->attach(MvcEvent::EVENT_RENDER, array(
            $this,
            'addDebugOverlay'
        ), 100);
    }

    public function getMvcDuration(MvcEvent $event)
    {
        // get Service Manager
        $serviceManager = $event->getApplication()->getServiceManager();
        $timer = $serviceManager->get('timer');
        $duration = $timer->stop('mvc-execution');
        error_log("MVC Duration:" . $duration . "seconds");
    }

    public function handleError(MvcEvent $event)
    {
        $controller = $event->getController();
        $error = $event->getParam('error');
        $exception = $event->getParam('exception');
        $message = 'Error:' . $error;
        if ($exception instanceof \Exception) {
            $message .= ', Exception(' . $exception->getMessage() . '): ' . $exception->getTraceAsString();
        }
        error_log($message);
    }

    public function addDebugOverlay(MvcEvent $event)
    {
        $viewModel = $event->getViewModel();
        $sidebarView = new ViewModel();
        $sidebarView->setTemplate('debug/layout/sidebar');
        $sidebarView->addChild($viewModel, 'content');
        $event->setViewModel($sidebarView);
    }
}
