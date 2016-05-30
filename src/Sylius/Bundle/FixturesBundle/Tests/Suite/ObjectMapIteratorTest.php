<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Tests\Suite;

use Sylius\Bundle\FixturesBundle\Suite\ObjectMapIterator;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ObjectMapIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_is_an_iterator()
    {
        static::assertInstanceOf(\Iterator::class, new ObjectMapIterator([], []));
    }

    /**
     * @test
     */
    public function it_is_iterable()
    {
        $data = [
            'keys' => [new \stdClass(), new \stdClass()],
            'values' => ['value 1', 'value 2'],
        ];

        $iterator = new ObjectMapIterator($data['keys'], $data['values']);

        $i = 0;
        foreach ($iterator as $key => $value) {
            static::assertSame($data['keys'][$i], $key);
            static::assertSame($data['values'][$i], $value);

            ++$i;
        }
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_exception_if_number_of_keys_and_values_is_not_the_same()
    {
        new ObjectMapIterator([new \stdClass()], ['first', 'second']);
    }
}
