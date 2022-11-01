<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

ExtensionManagementUtility::addStaticFile(
    'ot_imageupload',
    'Configuration/TypoScript',
    'Image Upload'
);
