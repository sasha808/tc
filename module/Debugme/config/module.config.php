<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Debug\Controller\Index' => 'Debug\Controller\IndexController'
        )
    ),
    'router' => array(
        'routes' => array(
            'debug' => array(
                'type' => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route' => '/index',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Debug\Controller',
                        'controller' => 'Index',
                        'action' => 'index'
                    )
                )
            ),
            // Simply drop new controllers in, and you can access them
            // using the path /debug/:controller/:action
            'debug' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/debug',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Debug\Controller',
                        'controller' => 'Index',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    // This route is a sane default when developing a module;
                    // as you solidify the routes for your module, however,
                    // you may want to remove it and replace it with more
                    // specific routes.
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array()
                        )
                    )
                )
            )
        )
    ),
    'timer' => array (
    		'times_as_float' => true,
    ),
    'service_manager' => array(
        'factories' => array(
    	   'timer' => 'Debug\Service\Factory\Timer',
        ),
        'initializers' => array(
            'Debug\Service\Initializer\DbProfiler',
            //'Debug\Service\Initializer\CacheProfiler',
        ),
        'aliases' => array(
            'Debug\Timer' => 'timer',
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Debug' => __DIR__ . '/../view'
        )
    )
);
