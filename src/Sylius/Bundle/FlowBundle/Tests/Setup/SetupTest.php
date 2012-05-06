<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Tests\Setup;

use Sylius\Bundle\FlowBundle\Setup;

class SetupTest extends \PHPUnit_Framework_TestCase
{
    public function testAlias()
    {
        $setup = $this->getSetup();
        $this->assertNull($setup->getAlias());

        $setup->setAlias('testing setup');
        $this->assertEquals('testing setup', $setup->getAlias());
    }

    public function testCountSteps()
    {
        $setup = $this->getSetup();
        $this->assertEquals(0, $setup->countSteps());

        $setup->setStep(0, new TestStep());
        $setup->setStep(1, new TestStep());
        $setup->setStep(2, new TestStep());

        $this->assertEquals(3, $setup->countSteps());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionWhenStepWithGivenIndexIsNotSet()
    {
        $setup = $this->getSetup();
        $step = $setup->getStep(0);
    }

    protected function getSetup()
    {
        return $this->getMockForAbstractClass('Sylius\Bundle\FlowBundle\Setup\Setup');
    }
}
