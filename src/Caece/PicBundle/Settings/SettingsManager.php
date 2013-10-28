<?php
namespace Caece\PicBundle\Settings;

use Doctrine\Common\Inflector\Inflector;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Yaml\Yaml;

/**
 * Description of SettingsManager
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 * 
 * @DI\Service("caecepic_settings_manager")
 */
class SettingsManager
{
    /**
     * @var Settings
     */
    private $settings;
    
    private $lastMtime;
    
    private $configFile;
    
    private $defaultConfigFile;
    
    /**
     * @DI\InjectParams({
     *     "configFile" = @DI\Inject("%kernel.root_dir%/settings/settings.yml"),
     *     "defaultConfigFile" = @DI\Inject("%kernel.root_dir%/config/settings_default.yml")
     * })
     */
    function __construct($configFile, $defaultConfigFile)
    {
        $this->configFile = $configFile;
        $this->defaultConfigFile = $defaultConfigFile;
    }
    
    private function failOnUnloadedSettings()
    {
        if ($this->settings === null) {
            throw new \LogicException('Se debe cargar la configuración con loadSettings antes acceder a estas');
        }
    }
    
    private function getConfigFileMtime()
    {
        clearstatcache();
        return filemtime($this->configFile);
    }
    
    private function setScalarValuesToObject($object, $values)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        
        foreach ($values as $key => $value) {
            if (is_scalar($value)) {
                try {
                    $accessor->setValue($object, $key, $value);
                } catch (NoSuchPropertyException $e) {
                    //ignorar
                }
            }
        }
    }
    
    public function loadSettings()
    {
        $this->settings = new Settings();
        
        $yaml = array_replace_recursive(
            Yaml::parse(file_get_contents($this->defaultConfigFile)),
            file_exists($this->configFile)? Yaml::parse(file_get_contents($this->configFile)):array()
        );
        
        $this->setScalarValuesToObject($this->settings, $yaml['settings']);
        
        foreach ($yaml['settings']['channels'] as $key => $value) {
            $channel = new ChannelSetting();
            $this->setScalarValuesToObject($channel, $value);
            $channel->setNumber((int)$key);
            $this->settings->getChannels()->set($channel->getNumber(), $channel);
        }
        
        $this->lastMtime = file_exists($this->configFile)? $this->getConfigFileMtime():0;
        
        return $this->settings;
    }
    
    private function setScalarPropertiesToArray($object, &$array)
    {
        $class = new \ReflectionClass($object);
        $accessor = PropertyAccess::createPropertyAccessor();
        
        foreach ($class->getProperties() as $property) {
            $pname = $property->getName();
            $pvalue = $accessor->getValue($object, $pname);
            
            if (is_scalar($pvalue) || $pvalue === null) {
                $array[Inflector::tableize($pname)] = $pvalue;
            }
        }
    }
    
    public function saveSettings()
    {
        $this->failOnUnloadedSettings();
        
        $settings = array();
        
        $this->setScalarPropertiesToArray($this->settings, $settings);
        
        $settings['channels'] = array();
        foreach ($this->settings->getChannels() as $channel) {
            $settings['channels'][$channel->getNumber()] = array();
            $this->setScalarPropertiesToArray($channel, $settings['channels'][$channel->getNumber()]);
        }
        
        file_put_contents($this->configFile, Yaml::dump(array(
            'settings' => $settings
        ), 10));
        
        $this->lastMtime = $this->getConfigFileMtime();
    }
    
    public function wasChanged()
    {
        $this->failOnUnloadedSettings();
        return file_exists($this->configFile)?
            $this->getConfigFileMtime() > $this->lastMtime:
            false;
    }
    
    public function getSettings()
    {
        $this->failOnUnloadedSettings();
        return $this->settings;
    }
}
