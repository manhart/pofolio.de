<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * GUI_Frame.php created on 12.09.23, 22:29.
 */

namespace pofolio\guis\GUI_Frame;

use GUI_CustomFrame;
use pool\includes\Resources;

class GUI_Frame extends GUI_CustomFrame
{
    /**
     * @var array<string, string> $templates files (templates) to be loaded, usually used with $this->Template->setVar(...) in the prepare function.
     *     Defined as an associated array [handle => tplFile].
     */
    protected array $templates = [
        'stdout' => 'tpl_frame.html',
    ];

    /**
     * Templates laden
     */
    public function loadFiles()
    {
        parent::loadFiles();

        $className = __CLASS__;


        $min = '.min';
        $minDir = 'min/';

        /**
         * Language / Locales: translate to different versions
         */
        $locale = $this->Weblication->getLocale();
        $locale_short = substr($locale, 0, strrpos($locale, '.'));


        if(IS_TESTSERVER) {
            $min = '';
            $minDir = '';
        }

        // external libraries:
        // Pace-Loader
        $this->getHeadData()->addStyleSheet($this->Weblication->findStyleSheet('pace-loader.css', $className));
        Resources\JS__pace::addResourceTo($this->getHeadData(), IS_PRODUCTION);

        // Bootstrap ----------------------------------------------------------------------------
        $bootstrapVersion = '5.3.1';
        $bootstrapPath = addEndingSlash(DIR_RELATIVE_3RDPARTY_ROOT).'bootstrap/'.$bootstrapVersion;

        $jsFile = addEndingSlash($bootstrapPath).'js/bootstrap'.$min.'.js';
        $this->getHeadData()->addJavaScript($jsFile);

        // Bootstrap CSS
        $cssFile = addEndingSlash($bootstrapPath).'css/bootstrap'.$min.'.css';
        $this->getHeadData()->addStyleSheet($cssFile);

        // Tabulator ----------------------------------------------------------------------------
        Resources\CSS_tabulator::addResourceTo($this->getHeadData(), IS_PRODUCTION, resource: Resources\dir\Dir_tabulator::BS5);
        Resources\JS__tabulator::addResourceTo($this->getHeadData(), IS_PRODUCTION);

        // Url --------------------------------------------------------------------------------------------
        $this->getHeadData()->addJavaScript($this->Weblication->findJavaScript('url.js', '', true));

        $appCSS = $this->Weblication->findStyleSheet('app.css');
        $this->getHeadData()->addStyleSheet($appCSS);

        $appJS = $this->Weblication->findJavaScript('app.js');
        $this->addScriptFileAtTheEnd($appJS);
    }

    /**
     * prepare template & other
     */
    protected function create(): void
    {
        $appVersionTitle = ($version = $this->Weblication->getVersion()) ? 'Version: '.$version : '';
        $this->Template->setVar('APP_VERSION_TITLE', $appVersionTitle);
    }
}