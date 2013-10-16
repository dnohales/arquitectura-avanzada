<?php

namespace Caece\PicBundle\Controller;

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
        return array();
    }
}
