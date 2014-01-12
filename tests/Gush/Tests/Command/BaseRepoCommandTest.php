<?php

/*
 * This file is part of Gush.
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Tests\Command;

use Gush\Command\ConfigureCommand;
use Symfony\Component\Yaml\Yaml;
use Gush\Command\BaseRepoCommand;

class BaseRepoCommandTest extends BaseTestCase
{
    protected $command;
    protected $input;

    public function setUp()
    {
        $this->command = new BaseRepoCommand('foo');

        $this->input = $this->getMock(
            'Symfony\Component\Console\Input\InputInterface'
        );
    }

    public function provideGetOrgRepo()
    {
        return array(
            array('foo/bar', array('foo', 'bar')),
            array(null, array('cordoval', 'gush')),
            array('asdasd', array('cordoval', 'gush'), true),
        );
    }

    /**
     * @dataProvider provideGetOrgRepo
     */
    public function testGetOrgRepo($repo, $expected, $exception = false)
    {
        if ($exception) {
            $this->setExpectedException('InvalidArgumentException', 'invalid repository');

        }
        $this->input->expects($this->once())
            ->method('getOption')
            ->with('repo')
            ->will($this->returnValue($repo));

        $res = $this->command->getOrgAndRepo($this->input);
        $this->assertEquals($expected, $res);
    }
}

