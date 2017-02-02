<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\Tests\Normalizer;

use Sylius\Bundle\ApiBundle\Normalizer\LocaleAwareCamelKeysNormalizer;

/**
 * @see \FOS\RestBundle\Tests\Normalizer\CamelKeysNormalizerTest
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class LocaleAwareCamelKeysNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     *
     * @expectedException \FOS\RestBundle\Normalizer\Exception\NormalizationException
     */
    public function it_does_not_override_existing_keys()
    {
        $normalizer = new LocaleAwareCamelKeysNormalizer();
        $normalizer->normalize([
            'foo' => [
                'foo_bar' => 'foo',
                'foo_Bar' => 'foo',
            ],
        ]);
    }

    /**
     * @test
     *
     * @dataProvider normalizeProvider
     */
    public function it_normalizes_snake_cased_keys_to_camel_case(array $array, array $expected)
    {
        $normalizer = new LocaleAwareCamelKeysNormalizer();
        $this->assertEquals($expected, $normalizer->normalize($array));
    }

    /**
     * @test
     *
     * @dataProvider localeAwareNormalizeProvider
     */
    public function it_does_not_normalize_locales_codes(array $array, array $expected)
    {
        $normalizer = new LocaleAwareCamelKeysNormalizer();
        $this->assertEquals($expected, $normalizer->normalize($array));
    }

    /**
     * @return array
     */
    public function normalizeProvider()
    {
        $array = $this->normalizeProviderCommon();
        $array[] = [[
            '__username' => 'foo',
            '_password' => 'bar',
            '_foo_bar' => 'foobar',
        ], [
            '_Username' => 'foo',
            'Password' => 'bar',
            'FooBar' => 'foobar',
        ]];

        return $array;
    }

    /**
     * @return array
     */
    public function localeAwareNormalizeProvider()
    {
        $array = $this->normalizeProviderCommon();
        $array[] = [['translations' => [
            'en_US' => ['foo_bar' => 'mops'],
            'nl_NL' => ['bar' => 'narwhale_io']],
        ], ['translations' => [
            'en_US' => ['fooBar' => 'mops'],
            'nl_NL' => ['bar' => 'narwhale_io']],
        ]];

        return $array;
    }

    /**
     * @return array
     */
    private function normalizeProviderCommon()
    {
        return [
            [[], []],
            [
                ['foo' => ['Foo_bar_baz' => ['foo_Bar' => ['foo_bar' => 'foo_bar']]],
                    'foo_1ar' => ['foo_bar'],
                ],
                ['foo' => ['FooBarBaz' => ['fooBar' => ['fooBar' => 'foo_bar']]],
                    'foo1ar' => ['foo_bar'],
                ],
            ],
        ];
    }
}