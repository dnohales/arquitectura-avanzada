<?php

namespace Caece\PicBundle\Controller;

use Caece\PicBundle\Form\SettingsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Description of AdminController
 *
 * @author Damián Nohales <damiannohales@gmail.com>
 * 
 * @Route("/admin")
 */
class AdminController extends Controller {
    
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $settingsManager = $this->get('caecepic_settings_manager');
        $form = $this->createForm(new SettingsType(), $settingsManager->loadSettings());
        
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            
            if ($form->isValid()) {
                $settingsManager->saveSettings();
            }
        }
        
        return array(
            'form' => $form->createView()
        );
    }
}
