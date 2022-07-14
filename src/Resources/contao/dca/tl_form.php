<?php

/**
 * HubSpot Forms Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2022, numero2 - Agentur für digitales Marketing GbR
 */


use Contao\CoreBundle\DataContainer\PaletteManipulator;


PaletteManipulator::create()
    ->addLegend('hubspot_legend', 'store_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('sendToHubspot', 'hubspot_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_form');


$GLOBALS['TL_DCA']['tl_form']['palettes']['__selector__'][] = 'sendToHubspot';
$GLOBALS['TL_DCA']['tl_form']['subpalettes']['sendToHubspot'] = 'hubspot_portal_id,hubspot_form_id';


$GLOBALS['TL_DCA']['tl_form']['fields']['sendToHubspot'] = [
    'exclude'           => true
,   'inputType'         => 'checkbox'
,   'filter'            => true
,   'eval'              => ['submitOnChange'=>true]
,   'sql'               => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_form']['fields']['hubspot_portal_id'] = [
    'exclude'           => true
,   'inputType'         => 'text'
,   'search'            => true
,   'eval'              => ['mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50']
,   'sql'               => "varchar(255) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_form']['fields']['hubspot_form_id'] = [
    'exclude'           => true
,   'inputType'         => 'text'
,   'search'            => true
,   'eval'              => ['mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50']
,   'sql'               => "varchar(255) NOT NULL default ''"
];
