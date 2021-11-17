<?php

namespace GingerPay\Payment\Tests;

require_once 'PathsToFiles/SetupAndServiceFiles.php';

use PHPUnit\Framework\TestCase;

class AfterMergeTest extends TestCase
{
    private $pathToFiles;

    public function setUp() : void
    {
        $this->pathToFiles = [
            \SetupAndServiceFiles::getServiceFiles(),
            \SetupAndServiceFiles::getSetupFiles()
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
