<?php
namespace Caece\PicBundle\Controller;

use Caece\PicBundle\Form\HistoryType;
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
class HistoryController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $settings = $this->get('caecepic_settings_manager')->loadSettings();
        $form = $this->createHistoryForm();
        $readings = null;
        
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            $readings = $this->findReadingsByForm($form);
            
            if ($form->get('downloadButton')->isClicked()) {
                return $this->generateCsvResponse($readings, $form);
            }
        }
        
        return $this->render('CaecePicBundle:History:index.html.twig', array(
            'form' => $form->createView(),
            'readings' => $readings,
            'settings' => $settings
        ));
    }
    
    private function createHistoryForm()
    {
        $settings = $this->get('caecepic_settings_manager')->getSettings();
        return $this->createForm(new HistoryType(), null, array(
            'settings' => $settings
        ));
    }
    
    private function findReadingsByForm($form)
    {
        $data = $form->getData();
        return $this->getDoctrine()->getEntityManager()->getRepository('CaecePicBundle:ChannelReading')->findByChannelBetweenDates(
            $data['channel'],
            $data['beginDate'],
            $data['endDate']
        );
    }

    public function generateCsvResponse(array $readings, $form)
    {
        $data = $form->getData();
        $settings = $this->get('caecepic_settings_manager')->getSettings();
        
        $response = new StreamedResponse(function() use($readings, $settings) {
            //Imprimo el UTF-8 BOM para compatibilidad con Excel
            echo "\xEF\xBB\xBF";
            
            //No se deben imprimir tildes porque Excel es una pija
            echo "ID;Valor leido;Valor convertido;Fecha de lectura\n";
            
            foreach ($readings as $r) {
                echo "{$r->getId()};{$r->getRawData()};{$settings->convertReading($r, true)};{$r->getReadedAt()->format('d/m/Y H:i:s')}\n";
            }
        });
        
        $response->headers->set('Content-Encoding', 'UTF-8');
        $response->headers->set('Content-Type', 'application/csv;charset=UTF-8');
        $filename = 'Historico_Canal'.$data['channel'].'_'.$data['beginDate']->format('Y-m-d').'_al_'.$data['endDate']->format('Y-m-d').'.csv';
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename));
        
        return $response;
    }
}
