<?php
namespace Debug\Service;

class Timer {
	
    protected $start;
    protected $timeAsFloat;
    
    public function __construct($timeAsFloat = false)
    {
    	$this->timeAsFloat = $timeAsFloat;
    }
    
    public function start($key)
    {
    	$this->start[$key] = microtime($this->timeAsFloat);
    }
    
    public function stop($key) 
    {
    	if(!isset($this->start[$key])) {
    		return null;
    	}
    	
    	return microtime($this->timeAsFloat) - $this->start[$key];
    }
    
}