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

use Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilder;

class TestProcessBuilder extends ProcessBuilder
{
    /**
     * Method getProcess exists only in TestProcessBuilder to allow testing
     */
    public function getProcess()
    {
        return $this->process;
    }
}
