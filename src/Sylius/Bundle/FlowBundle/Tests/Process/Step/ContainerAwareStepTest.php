<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Tests\Process\Step;

use Sylius\Bundle\FlowBundle\Tests\Fixtures\TestContainerAwareStep;

/**
 * ContainerAwareStepTest test.
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class ContainerAwareStepTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Process\Step\ContainerAwareStep::setContainer
     */
    public function shouldInjectContainerBySetter()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $step = new TestContainerAwareStep();
        $step->setContainer($container);

        $this->assertSame($step->getContainer(), $container);
    }
}
