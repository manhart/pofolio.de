<?php
/*
 * This file is part of the pofolio project.
 *
 * (c) Alexander Manhart IT
 *
 * index.php created on 07.05.23, 21:35.
 */

namespace pofolio;

// global location for the configuration files
 $_SERVER['_BaseNamespacePath'] = '/virtualweb/manhart';
const DIR_CONFIGS_ROOT = __DIR__.'/config';
require_once DIR_CONFIGS_ROOT.'/config.inc.php'; // <-- innerhalb config.inc.php die Pfade anpassen!
require_once '../pool/pool.lib.php';
require_once DIR_3RDPARTY_ROOT.'/_3rdPartyResources.php';


use Exception;
use pofolio\classes\PofolioApp;
use pofolio\guis\GUI_Frame\GUI_Frame;

$App = PofolioApp::getInstance();
//$App->addDataInterface($MariaDB);

// check schema, access rights, user settings (pw change)
//try {
//    $App->checkInstallation();
//} catch (Exception $e) {
//    $App->renderException($e);
//}
try {
    $App->setup([
        'application.name' => 'pofolio',
        'application.title' => 'Pofolio',
        'application.launchModule' => GUI_Frame::class,
    ]);

    $App->render();
}
catch (Exception $e) {
    throw $e;
}