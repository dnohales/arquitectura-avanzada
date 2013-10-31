<?php
namespace Caece\PicBundle\Controller;

use Caece\PicBundle\Form\DownloadHistoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of DownloadHistoryController
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 * 
 * @Route("/history")
 */
class DownloadHistoryController extends Controller
{
    /**
     * @Route("/get_form")
     */
    public function getFormAction()
    {
        $form = $this->createForm(new DownloadHistoryType(), null, array(
            'settings' => $this->get('caecepic_settings_manager')->loadSettings()
        ));
        
        return $this->render('CaecePicBundle:DownloadHistory:_form.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    /**
     * @Route("/process_form", methods={"POST"})
     */
    public function processFormAction()
    {
        $settings = $this->get('caecepic_settings_manager')->loadSettings();
        $form = $this->createForm(new DownloadHistoryType(), null, array(
            'settings' => $settings
        ));
        $form->bind($this->getRequest());
        
        $data = $form->getData();
        $readings = $this->getDoctrine()->getEntityManager()->getRepository('CaecePicBundle:ChannelReading')->findByChannelBetweenDates(
            $data['channel'],
            $data['beginTime'],
            $data['endTime']
        );
        
        $response = new StreamedResponse(function() use($readings, $settings) {
            //Imprimo el UTF-8 BOM para compatibilidad con Excel
            echo "\xEF\xBB\xBF";
            
            //No se deben imprimir tildes porque Excel es una pija
            echo "ID;Leido;Raw;Convertido\n";
            
            foreach ($readings as $r) {
                echo "{$r->getId()};{$r->getReadedAt()->format('Y-m-d H:i:s')};{$r->getRawData()};{$settings->convertReading($r)}\n";
            }
        });
        
        $response->headers->set('Content-Encoding', 'UTF-8');
        $response->headers->set('Content-Type', 'application/csv;charset=UTF-8');
        $filename = 'Historico_Canal'.$data['channel'].'_'.$data['beginTime']->format('Y-m-d\TH-i-s').'_al_'.$data['endTime']->format('Y-m-dTH-i-s').'.csv';
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename));
        
        return $response;
    }
}
