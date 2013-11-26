<?php
namespace Caece\PicBundle\Form;

use Caece\PicBundle\PicSensor\Sensor\SensorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of SettingsType
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class ChannelSettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('active', 'checkbox', array(
            'label' => 'Activado',
            'required' => false
        ));
        
        $builder->add('beginThreshold', 'number', array());
        $builder->add('endThreshold', 'number', array());
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caece\\PicBundle\\Settings\\ChannelSetting'
        ));
    }
    
    public function getName()
    {
        return 'caece_pic_settings';
    }
}
