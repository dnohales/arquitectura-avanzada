<?php
namespace Caece\PicBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of SettingsType
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('notifyEmailAddress', 'email', array(
            'label' => 'Dirección de correo para alarmas'
        ));
        
        $builder->add('sampleInterval', 'number', array(
            'label' => 'Intervalo de tiempo entre muestras',
        ));
        
        $builder->add('lightScheduleEnabled', 'checkbox', array(
            'label' => 'Activar encendido automático de luces',
            'required' => false
        ));
        
        $builder->add('lightStartTime', 'text', array());
        
        $builder->add('lightEndTime', 'text', array());
        
        $builder->add('channels', 'collection', array(
            'type' => new ChannelSettingType(),
        ));
        
        $builder->add('save', 'submit', array(
            'label' => 'Guardar configuración'
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caece\\PicBundle\\Settings\\Settings'
        ));
    }
    
    public function getName()
    {
        return 'caece_pic_settings';
    }
}
