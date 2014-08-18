<?php

class S_305_HealthCheckScannerCasesTestWrapper extends HealthCheckScannerCasesTestWrapper
{
    public function init()
    {
        if (parent::init()) {
            $this->tearDown();
            return true;
        }
        return false;
    }

    public function tearDown()
    {
        unset($GLOBALS['dictionary']['Account']);
        $GLOBALS['reload_vardefs'] = true;
        new Account();
        $GLOBALS['reload_vardefs'] = null;
    }
}
