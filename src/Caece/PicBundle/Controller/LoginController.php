<?php

namespace Caece\PicBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Description of LoginController
 *
 * @author Damián Nohales <damiannohales@gmail.com>
 */
class LoginController {
    
    /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction()
    {
        return array();
    }
}
