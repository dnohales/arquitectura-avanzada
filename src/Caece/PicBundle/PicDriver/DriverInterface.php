<?php
namespace Caece\PicBundle\PicDriver;

/**
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
interface DriverInterface
{
    function connect($connectinData);
    function send($command);
    function read();
}

?>
