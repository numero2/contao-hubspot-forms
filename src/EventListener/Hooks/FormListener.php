<?php

/**
 * HubSpot Forms Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2022, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\HubSpotFormsBundle\EventListener\Hooks;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Form;
use Contao\System;
use Exception;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Component\HttpFoundation\RequestStack;


class FormListener {


    /**
     * @var string
     */
    const API_ENDPOINT = "https://api.hsforms.com/submissions/v3/integration/submit/";


    /**
    * @var RequestStack
    */
    private $requestStack;


    public function __construct( RequestStack $requestStack ) {

        $this->requestStack = $requestStack;
    }


    /**
     * send the submitted data to hubspot if the form is configured for hubspot
     *
     * @param array $submittedData
     * @param array $formData
     * @param array $files
     * @param array $labels
     * @param Form $form
     *
     * @Hook("processFormData")
     */
    public function sendData( array $submittedData, array $formData, ?array $files, array $labels, Form $form ): void {

        if( !empty($formData['sendToHubspot']) && !empty($formData['hubspot_portal_id']) && !empty($formData['hubspot_form_id']) ) {

            $aData = [];

            // add submitted data
            foreach( $submittedData as $key => $value ) {
                $aData['fields'][] = [
                    'objectTypeId' => "0-1",
                        // Contact: 0-1
                        // Company: 0-2
                        // Deal: 0-3
                        // Ticket: 0-5
                    'name' => $key,
                    'value' => $value
                ];
            }

            // add context data
            $request = $this->requestStack->getCurrentRequest();
            $aData['context']['pageUri'] = $request->getSchemeAndHttpHost() . $request->getPathInfo();


            if( !empty($aData['fields']) ) {

                $this->sendDataToHubSpot($formData['hubspot_portal_id'], $formData['hubspot_form_id'], $aData, $formData);

            } else {

                System::log('Form (ID: ' . $formData['id'] . ') was not sent to HubSpot as it has no fields to submit', __METHOD__, TL_GENERAL);
            }
        }
    }


    /**
     * send the acutal data to hubspot
     *
     * @param string $portalId
     * @param string $formId
     * @param array $aData
     * @param array $formData
     *
     * @return bool
     */
    protected function sendDataToHubSpot( string $portalId, string $formId, array $aData, array $formData  ): bool {

        $oOptions = new HttpOptions();
        $oOptions->setHeaders(["Content-Type" => "application/json"]);
        $oOptions->setBody(json_encode($aData));

        $oClient = HttpClient::create();
        $aOptions = $oOptions->toArray();

        $url = self::API_ENDPOINT . $portalId . '/' . $formId;

        $oResponse = $oClient->request('POST', $url, $aOptions);

        try {

            if( $oResponse->getStatusCode() === 200 ) {

                System::log('Form (ID: ' . $formData['id'] . ') was successfully sent to HubSpot', __METHOD__, TL_GENERAL);
                return true;

            } else {

                throw new Exception('HTTP status code: '. $oResponse->getStatusCode() .'. '. $oResponse->getContent());
            }

        } catch( Exception $e) {
            System::log('Form (ID: ' . $formData['id'] . ') was not sent to HubSpot. Error: ' . $e->getMessage(), __METHOD__, TL_ERROR);
        }

        return false;
    }
}
