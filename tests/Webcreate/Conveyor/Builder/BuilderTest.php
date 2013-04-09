<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

use Webcreate\Conveyor\Builder\Builder;

class BuilderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->tempdir = sys_get_temp_dir();
    }

    public function testBuilderCallsExecuteOnTasks()
    {
        $task1 = $this->getMockBuilder('Webcreate\\Conveyor\\Task\\Task')->disableOriginalConstructor()->getMock();
        $task1
            ->expects($this->once())
            ->method('execute')
            ->withAnyParameters()
        ;
        $task1
            ->expects($this->any())
            ->method('supports')
            ->will($this->returnValue(true))
        ;

        $version = $this->getMockBuilder('Webcreate\\Conveyor\\Repository\\Version')->getMock();

        $builder = new Builder($this->tempdir, array($task1));
        $builder->build('test', $version);
    }

    public function testGetBuilddir()
    {
        $builder = new Builder($this->tempdir, array());

        $this->assertEquals($this->tempdir, $builder->getBuildDir());
    }
}