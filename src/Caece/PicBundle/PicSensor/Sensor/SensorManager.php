<?php
namespace Caece\PicBundle\PicSensor\Sensor;

use Symfony\Component\Finder\Finder;

/**
 * Description of SensorManager
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class SensorManager
{
    const DUMMY_SENSOR_CLASS = 'Caece\PicBundle\PicSensor\Sensor\Concrete\DummySensor';
    
    private static $singletonInstance;
    
    private $sensorsMap;
    
    private function __construct()
    {
        $this->createSensorsMap();
    }
    
    /**
     * @return SensorManager La instancia singleton de esta clase
     */
    public static function getInstance()
    {
        if (self::$singletonInstance === null) {
            self::$singletonInstance = new self;
        }
        
        return self::$singletonInstance;
    }
    
    private function createSensorsMap()
    {
        $this->sensorsMap = array();
        
        $finder = Finder::create();
        $finder->files()->name('*.php')->in(__DIR__.'/Concrete');
        
        foreach ($finder as $file) {
            $className = __NAMESPACE__.'\\Concrete\\'.str_replace(array('.php', '/'), array('', '\\'), $file->getRelativePathname());
            
            $class = new \ReflectionClass($className);
            
            if ($class->isInstantiable()) {
                $this->sensorsMap[$className] = $class->newInstance();
            }
        }
    }
    
    public function getAvailableSensors()
    {
        return $this->sensorsMap;
    }
    
    public function getSensorByClassName($className)
    {
        return isset($this->sensorsMap[$className])? $this->sensorsMap[$className]:null;
    }
    
    public function getDummySensor()
    {
        return $this->getSensorByClassName(self::DUMMY_SENSOR_CLASS);
    }
}
