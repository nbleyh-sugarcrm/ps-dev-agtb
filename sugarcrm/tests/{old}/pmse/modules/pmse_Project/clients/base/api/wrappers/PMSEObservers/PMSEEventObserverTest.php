<?php
//FILE SUGARCRM flav=ent ONLY
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

use PHPUnit\Framework\TestCase;

class PMSEEventObserverTest extends TestCase
{
    public function testUpdate()
    {
        $eventObserverMock = $this->getMockBuilder('PMSEEventObserver')
                ->disableOriginalConstructor()
                ->setMethods(NULL)
                ->getMock();
        
        $loggerMock = $this->getMockBuilder('PMSELogger')
                ->disableOriginalConstructor()
                ->setMethods(array('info', 'debug', 'error'))
                ->getMock();
        
        $subjectMock = $this->getMockBuilder('PMSESubject')
                ->disableOriginalConstructor()
                ->setMethods(array('getEvent', 'getEventDefinition', 'getProcessDefinition'))
                ->getMock();
        
        $eventMock = $this->getMockBuilder('PMSEEvent')
                ->disableOriginalConstructor()
                ->setMethods(NULL)
                ->getMock();
        $eventMock->fetched_row = array();
        
        $eventDefMock = $this->getMockBuilder('PMSEEventDefinition')
                ->disableOriginalConstructor()
                ->setMethods(NULL)
                ->getMock();
        $eventDefMock->fetched_row = array();
        
        $processDefMock = $this->getMockBuilder('PMSEProcessDefinition')
                ->disableOriginalConstructor()
                ->setMethods(NULL)
                ->getMock();
        
        $processDefMock->fetched_row = array();
        
        $subjectMock->expects($this->once())
                ->method('getEvent')
                ->will($this->returnValue($eventMock));
        
        $subjectMock->expects($this->once())
                ->method('getEventDefinition')
                ->will($this->returnValue($eventDefMock));
        
        $subjectMock->expects($this->once())
                ->method('getProcessDefinition')
                ->will($this->returnValue($processDefMock));
        
        $eventObserverMock->setLogger($loggerMock);
        
        $relatedDepenedencyMock = $this->getMockBuilder('PMSERelatedDependencyWrapper')
                ->disableOriginalConstructor()
                ->setMethods(array('processRelatedDependencies'))
                ->getMock();
        
        $eventObserverMock->setRelatedDependency($relatedDepenedencyMock);
        
        $eventObserverMock->update($subjectMock);
    }
}
