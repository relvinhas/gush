<?php

/*
 * This file is part of Gush.
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Tests\Helper;

use Gush\Helper\TextHelper;

class TextHelperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->textHelper = new TextHelper;
    }

    public function provideTruncate()
    {
        return array(
            array(
                'this is some text',
                5,
                null,
                null,
                'th...'
            ),
            array(
                'this is some text',
                5,
                'right',
                null,
                '...xt',
            ),
            array(
                'this is some text',
                5,
                'right',
                '-',
                '-text',
            ),
            array(
                'th',
                5,
                'right',
                '-',
                'th',
            ),
            array(
                'this is some more text',
                5,
                'right',
                '-----',
                '-----',
            ),
            array(
                'this is some more text',
                5,
                'right',
                '--------',
                '-----',
                'Delimiter length "8" cannot be greater',
            ),
        );
    }

    /**
     * @dataProvider provideTruncate
     */
    public function testTruncate($text, $length, $alignment, $truncateString, $expected, $expectedException = null)
    {
        if ($expectedException) {
            $this->setExpectedException('InvalidArgumentException', $expectedException);
        }
        $res = $this->textHelper->truncate($text, $length, $alignment, $truncateString);
        $this->assertEquals($expected, $res);
    }

    public function provideSlugify()
    {
        return array(
            array('this is some text', 'this-is-some-text'),
            array('voilà, j\'ai du texte', 'voila-j-ai-du-texte'),
            array('áçéeë', 'aceee'),
        );
    }

    /**
     * @dataProvider provideSlugify
     */
    public function testSlugify($string, $expected)
    {
        $string = $this->textHelper->slugify($string);
        $this->assertEquals($expected, $string);
    }
}
