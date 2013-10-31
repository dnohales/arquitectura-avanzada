<?php
namespace Caece\PicBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of DownloadHistoryType
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class DownloadHistoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $settings = $options['settings'];
        $channelChoices = array();
        
        foreach ($settings->getChannels() as $channel) {
            $channelChoices[$channel->getNumber()] = 'Canal Nº '.$channel->getNumber().': '.$channel->getSensor()->getName();
        }
        
        $builder->add('channel', 'choice', array(
            'label' => 'Canal',
            'choices' => $channelChoices,
        ));
        
        $builder->add('beginTime', 'datetime', array(
            'label' => 'Desde la fecha',
            'data' => new \DateTime('today')
        ));
        
        $builder->add('endTime', 'datetime', array(
            'label' => 'Hasta la fecha',
            'data' => new \DateTime()
        ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('settings'));
    }
    
    public function getName()
    {
        return 'caece_pic_history';
    }
}
