<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Tests\Storage;

use Sylius\Bundle\FlowBundle\Storage\Storage;

/**
 * Storage test.
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Storage\Storage
     */
    public function shouldSetDomainWhenInitialize()
    {
        $storage = $this->getMockForAbstractClass('Sylius\Bundle\FlowBundle\Storage\Storage');
        $storage->initialize('mydomain');

        $this->assertAttributeEquals(
            'mydomain',
            'domain',
            $storage
        );
    }
}
