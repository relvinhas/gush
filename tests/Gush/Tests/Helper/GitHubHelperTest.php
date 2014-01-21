<?php

namespace Gush\Tests\Helper;

use Gush\Helper\GitHubHelper;

class GitHubHelperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->gitHubHelper = new GitHubHelper();
    }

    public function provideValidateEnum()
    {
        return array(
            array('issue', 'filter', 'assigned'),
            array('foo', null, null, 'Unknown enum domain'),
            array('issue', 'foo', null, 'Unknown enum type'),
            array('issue', 'filter', 'foo', 'Unknown value'),
        );
    }

    /**
     * @dataProvider provideValidateEnum
     */
    public function testValidateEnum($domain, $type = null, $value = null, $exceptionMessage = null)
    {
        if (null !== $exceptionMessage) {
            $this->setExpectedException('InvalidArgumentException', $exceptionMessage);
        }

        $this->gitHubHelper->validateEnum($domain, $type, $value);
    }
}
