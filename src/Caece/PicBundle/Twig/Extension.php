<?php
namespace Caece\PicBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Environment;
use Twig_Extension;
use Twig_Function_Method;

/**
 * Description of Extension
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 * 
 * @DI\Service
 * @DI\Tag("twig.extension")
 */
class Extension extends Twig_Extension
{
    private $container;
    private $environment;
    
    /**
     * @DI\InjectParams({
     *     "container" = @DI\Inject("service_container"),
     * })
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function initRuntime(Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getFunctions()
    {
        return array(
            'get_parameter' => new Twig_Function_Method($this, 'generateGetParameter'),
        );
    }

    public function getName()
    {
        return 'caece_pic_twig_extension';
    }

    public function generateGetParameter($name)
    {
        return $this->container->getParameter($name);
    }
}
