<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Process\Scenario;

use Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilderInterface;

/**
 * Interface for process scenario.
 *
 * This interface should be implemented by all scenario you define.
 * For example, that can be a checkout or installation process scenario.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ProcessScenarioInterface
{
    /**
     * Builds the whole process.
     *
     * @param ProcessBuilderInterface $builder
     */
    public function build(ProcessBuilderInterface $builder);
}
