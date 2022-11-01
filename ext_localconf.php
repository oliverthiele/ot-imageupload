<?php

use OliverThiele\OtImageupload\Controller\UploadController;
use TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

call_user_func(
    function () {
        ExtensionUtility::configurePlugin(
            'OtImageupload',
            'Upload',
            [UploadController::class => 'form,upload'],
            // non-cacheable actions
            [UploadController::class => 'form,upload'],
            ExtensionUtility::PLUGIN_TYPE_PLUGIN
        );


        // Only include page.tsconfig if TYPO3 version is below 12 so that it is not imported twice.
        $versionInformation = GeneralUtility::makeInstance(Typo3Version::class);
        if ($versionInformation->getMajorVersion() < 12) {
            ExtensionManagementUtility::addPageTSConfig(
                '
                @import "EXT:ot_imageupload/Configuration/page.tsconfig"
            '
            );
        }

        $iconRegistry = GeneralUtility::makeInstance(
            IconRegistry::class
        );

        $iconRegistry->registerIcon(
            'ot-imageupload',
            FontawesomeIconProvider::class,
            [
                'name' => 'upload'
            ]
        );
    }
);
