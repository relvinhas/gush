<?php

namespace Gush\Tests\Twig;

use Gush\Twig\FilterExtension;

class FilterExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->extension = new FilterExtension();
    }

    public function provideUniq()
    {
        return [
            [
                'foo',
                [],
                [],
            ],
            [
                'milestone.title',
                [
                    [
                        'milestone' => ['title' => 'Version 1'],
                    ],
                    [
                        'milestone' => ['title' => 'Version 2'],
                    ],
                    [
                        'milestone' => ['title' => 'Version 1'],
                    ],
                ],
                [
                    [
                        'milestone' => ['title' => 'Version 1'],
                    ],
                    [
                        'milestone' => ['title' => 'Version 2'],
                    ],
                ],
            ],
            [
                'labels.name',
                [
                    [
                        'labels' => [
                            [ 'name' => 'Foobar' ],
                            [ 'name' => 'BarFoo' ],
                        ],
                    ],
                    [
                        'labels' => [
                            [ 'name' => 'BarFoo' ],
                        ],
                    ],
                    [
                        'labels' => [
                            [ 'name' => 'ZZZ' ],
                        ],
                    ],
                ],
                [
                    [
                        'labels' => [
                            [ 'name' => 'Foobar' ],
                            [ 'name' => 'BarFoo' ],
                        ],
                    ],
                    [
                        'labels' => [
                            [ 'name' => 'ZZZ' ],
                        ],
                    ],
                ],
                'Non-associative arrays not supported',
            ],
        ];
    }

    /**
     * @dataProvider provideUniq
     */
    public function testUniq($path, $data, $expected, $exceptionMessage = null)
    {
        if (null !== $exceptionMessage) {
            $this->setExpectedException('InvalidArgumentException', $exceptionMessage);
        }

        $res = $this->extension->uniq($data, $path);
        $this->assertEquals($expected, $res);
    }

    public function provideFilterEq()
    {
        return [
            [
                'foo', 'foo',
                [],
                [],
            ],
            [
                'milestone.title', 'Version 2',
                [
                    [
                        'milestone' => ['title' => 'Version 1'],
                    ],
                    [
                        'milestone' => ['title' => 'Version 2'],
                    ],
                    [
                        'milestone' => ['title' => 'Version 1'],
                    ],
                ],
                [
                    [
                        'milestone' => ['title' => 'Version 2'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideFilterEq
     */
    public function testFilterEq($path, $targetValue, $data, $expected)
    {
        $res = $this->extension->filterEq($data, $path, $targetValue);
        $this->assertEquals($expected, $res);
    }

}
