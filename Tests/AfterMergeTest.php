<?php

namespace GingerPay\Payment\Tests;

require_once 'PathsToFiles/SetupAndServiceFiles.php';
require_once 'PathsToFiles/RedefinersAndPluginFiles.php';
require_once 'PathsToFiles/LoggerAndObserverFiles.php';
require_once 'PathsToFiles/ModelFiles.php';
require_once 'PathsToFiles/LanguageAndBlockAndApiFiles.php';
require_once 'PathsToFiles/ControllerAndViewModuleFiles.php';
require_once 'PathsToFiles/EtcFiles.php';
require_once 'PathsToFiles/ViewFiles.php';

use PHPUnit\Framework\TestCase;

class AfterMergeTest extends TestCase
{
    private $pathToFiles;

    public function setUp() : void
    {
        $this->pathToFiles = [
            \SetupAndServiceFiles::getServiceFiles(),
            \SetupAndServiceFiles::getSetupFiles(),
            \RedefinersAndPluginFiles::getPluginFiles(),
            \RedefinersAndPluginFiles::getRedefinersFiles(),
            \LoggerAndObserverFiles::getLoggerFiles(),
            \LoggerAndObserverFiles::getObserverFile(),
            \ModelFiles::getModuleFiles(),
            \LanguageAndBlockAndApiFiles::getApiAndBlockFiles(),
            \LanguageAndBlockAndApiFiles::getLanguageFiles(),
            \ControllerAndViewModuleFiles::getControllerFiles(),
            \ControllerAndViewModuleFiles::getViewModuleFile(),
            \EtcFiles::getEtcFiles(),
            \ViewFiles::getViewFiles()
        ];
    }

    public function testIsFilesExist()
    {
        foreach ($this->pathToFiles as $subDirectoryFiles)
        {
            foreach ($subDirectoryFiles as $pathToFile)
            {
                $this->assertFileExists('../'.$pathToFile, 'Not found file in '. $pathToFile);
            }
        }
    }
}
