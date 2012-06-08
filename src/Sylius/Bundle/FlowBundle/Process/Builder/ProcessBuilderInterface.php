<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Process\Builder;

use Sylius\Bundle\FlowBundle\Process\ProcessInterface;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;

/**
 * Process build interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ProcessBuilderInterface
{
    /**
     * Build process by adding defined steps.
     *
     * @param ProcessInterface $process
     * @param array            $options
     */
    function build(ProcessInterface $process, array $options = array());

    /**
     * Add a step with given name.
     *
     * @param string               $name
     * @param string|StepInterface $step
     */
    function add($name, $step);

    /**
     * Remove step with given name.
     *
     * @param string $name
     */
    function remove($name);

    /**
     * Check wether or not process has given step.
     *
     * @param string $name
     */
    function has($name);

    /**
     * Register new step.
     *
     * @param string        $alias
     * @param StepInterface $step
     */
    function registerStep($alias, StepInterface $step);

    /**
     * Load step.
     *
     * @param string $alias
     *
     * @return StepInterface
     */
    function loadStep($alias);
}
