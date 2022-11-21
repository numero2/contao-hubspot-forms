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
use Contao\Widget;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * @Hook("addCustomRegexp")
 */
class CustomRgxpListener {


    /**
     * @var string
     */
    public const RGXP_NAME = 'fieldnameHubSpot';


    /**
    * @var TranslatorInterface
    */
    private $translator;


    public function __construct( TranslatorInterface $translator ) {
        $this->translator = $translator;
    }


    public function __invoke( string $regexp, $input, Widget $widget ): bool {

        if( self::RGXP_NAME !== $regexp ) {
            return false;
        }

        if( !preg_match('/^[A-Za-z0-9[\]_\.-]+$/', $input) ) {
            $widget->addError( $this->translator->trans('ERR.invalidFieldName', [], 'contao_default') );
        }

        return true;
    }
}
