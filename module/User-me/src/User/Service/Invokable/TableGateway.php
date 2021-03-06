<?php
namespace User\Service\Invokable;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\TableGateway\TableGateway as DbTableGateway;

class TableGateway implements ServiceLocatorAwareInterface
{

    /**
     *
     * @var ServiceLocatorInterface
     */
    protected $cache;

    protected $serviceLocator;

    public function get($tableName, $features = null, $resultSetPrototype = null)
    {
        $cacheKey = $tableName;
        // $cacheKey = md5(serialize($tableName.$features.$resultSetPrototype));
        if (isset($this->cache[$key])) {
            return $this->cache;
        }
        
        $config = $this->serviceLocator->get('config');
        // defined which class should be used for which table
        $tableGatewayMap = $config['table-gateway']['map'];
        if (isset($tableGatewayMap[$tableName])) {
            $className = $tableGatewayMap[$tableName];
            $this->cache[$cacheKey] = new $className();
        } else {
            $db = $this->serviceLocator->get('database');
            $this->cache[$cacheKey] = new DbTableGateway($tableName, $db, $features, $resultSetPrototype);
        }
        
        return $this->cache[$cacheKey];
    }
    /*
     * (non-PHPdoc) @see \Zend\ServiceManager\ServiceLocatorAwareInterface::setServiceLocator()
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    /*
     * (non-PHPdoc) @see \Zend\ServiceManager\ServiceLocatorAwareInterface::getServiceLocator()
     */
    public function getServiceLocator()
    {
        $this->serviceLocator;
    }
}