<?php

namespace GingerPay\Payment\Tests;

use PHPUnit\Framework\TestCase;

class AfterMergeTest extends TestCase
{
    public function setUp() : void
    {
    }

    public function testIsFilesExist()
    {
        $filenames = [
            "../registration.php",
            "../Setup/SetupData.php",
            //"adasda.php"
            ];

        foreach ($filenames as $pathToFile)
        {
            $this->assertFileExists($pathToFile, 'Not found file in '. $pathToFile);
        }
    }
}
