<?php

/*
 * This file is part of the Manager Tools.
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace ManagerTools\Tests;

use ManagerTools\Config;

/**
 * @author Daniel Gomes <me@danielcsgomes.com>
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testConfig()
    {
        $defaultConfig = Config::$defaultConfig;
        $config = new Config();

        $this->assertEquals($defaultConfig, $config->raw());


        $config->merge(array('foo' => 'bar'));

        $defaultConfig['foo'] = 'bar';
        $this->assertEquals($defaultConfig, $config->raw());
        $this->assertEquals('bar', $config->get('foo'));
        $this->assertTrue($config->has('foo'));

        $this->assertNull($config->get('foobar'));

        $this->assertFalse($config->isValid());
    }

    public function testConfigWithValidConfiguration()
    {
        $config = new Config();
        $config->merge(
            array(
                'github' => array(
                    'username' => 'foo',
                    'password' => 'bar'
                ),
                'cache-dir' => sys_get_temp_dir()
            )
        );

        $this->assertTrue($config->isValid());
    }
}
