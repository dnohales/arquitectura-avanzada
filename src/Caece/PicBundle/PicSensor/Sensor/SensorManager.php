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
            $className = __NAMESPACE__.'\\Concrete\\'.str_replace($file->getRelativePathname, array(
                '.php' => '',
                '/' => '\\'
            ));
            
            $class = new \ReflectionClass($className);
            
            if ($class->isInstantiable()) {
                $this->sensorsMap[$className] = $class->newInstance();
            }
        }
    }
    
    public function getAvailableSensors()
    {
        return array_values($this->sensorsMap);
    }
    
    public function getSensorByClassName($className)
    {
        return $this->sensorsMap[$className];
    }
}
