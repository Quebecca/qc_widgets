<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(static function () {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig(
        "@import 'EXT:qc_widgets/Configuration/TSconfig/User/options.tsconfig'"
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
        "@import 'EXT:qc_widgets/Configuration/TypoScript/setup.typoscript'"
    );
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    foreach (['extension', 'dashboard', 'extensions', 'issue', 'merge'] as $icon) {
        $iconRegistry->registerIcon(
            'tx-qc_widgets-' . $icon . '-icon',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:qc_widgets/Resources/Public/Icons/' . \ucfirst($icon) . '.svg']
        );
    }
});
