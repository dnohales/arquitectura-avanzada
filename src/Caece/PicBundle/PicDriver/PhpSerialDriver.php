<?php
namespace Caece\PicBundle\PicDriver;

use Symfony\Component\Process\Process;

/**
 * Description of PIC18F2550Driver
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class PhpSerialDriver implements DriverInterface
{
    private $serial;
    
    public function connect($connectinData)
    {
        $defaults = array(
            'port' => 'COM1',
            'baudRate' => 115200,
            'characterLength' => 8,
            'flowControl' => 'none',
            'parity' => 'none',
            'stopBits' => 1,
            'binDirectory' => ''
        );
        $config = array_replace($defaults, $connectinData);
        
        $this->serial = new PhpSerial();
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $process = new Process('comconfig.exe '.$config['port']);
            $process->setWorkingDirectory($connectinData['binDirectory']);
            $process->run();
            echo '[comconfig] '.$process->getOutput()."\n";
        }

        if (!@$this->serial->deviceSet($config['port'])) {
            throw new DriverException('No se puede asignar el puerto '.$config['port']);
        }

        $this->serial->confBaudRate($config['baudRate']);
        $this->serial->confCharacterLength($config['characterLength']);
        $this->serial->confFlowControl($config['flowControl']);
        $this->serial->confParity($config['parity']);
        $this->serial->confStopBits($config['stopBits']);

        if (!@$this->serial->deviceOpen()) {
            throw new DriverException('No se puede abrir el dispositivo');
        }
    }

    public function read()
    {
        $data = $this->serial->readPort();
        
        if ($data != '') {
            //Separo las lecturas por el delimitador <LF><CR>
            $separatedData = explode("\n\r", trim($data, "\n\r"));
            return $separatedData;
        } else {
            return array();
        }
    }

    public function send($command)
    {
        $this->serial->sendMessage($command, 0);
    }
    
    public function sendAsHex($command)
    {
        $this->send(hex2bin(str_replace(' ', '', $command)));
    }
}
