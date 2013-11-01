<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Tests\Fixtures;

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\FlowBundle\Process\Step\ContainerAwareStep;

class TestContainerAwareStep extends ContainerAwareStep
{
    /**
     * Just for check if container setter works
     */
    public function getContainer()
    {
        return $this->container;
    }

    public function displayAction(ProcessContextInterface $context)
    {
        // pufff.
    }
}
