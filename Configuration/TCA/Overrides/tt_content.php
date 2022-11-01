<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

ExtensionUtility::registerPlugin(
    'OtImageupload',
    'Upload',
    'Upload Image'
);


$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['otimageupload_upload'] = 'pages,layout,select_key,recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['otimageupload_upload'] = 'pi_flexform';

ExtensionManagementUtility::addPiFlexFormValue(
    'otimageupload_upload',
    'FILE:EXT:ot_imageupload/Configuration/FlexForms/PluginSettings.xml'
);

if (isset($GLOBALS['TCA']['tt_content']['types']['otimageupload_upload']) && is_array($GLOBALS['TCA']['tt_content']['types']['otimageupload_upload'])) {
    $GLOBALS['TCA']['tt_content']['types']['otimageupload_upload'] = array_replace_recursive(
        $GLOBALS['TCA']['tt_content']['types']['otimageupload_upload'],
        [
            'showitem' => '
                --div--;General,
                --palette--;General;general,
                --palette--;Headers;headers,
                --div--;Options,
                pi_flexform'
        ]
    );
}
