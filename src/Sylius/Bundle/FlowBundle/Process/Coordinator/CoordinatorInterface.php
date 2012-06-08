<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Process\CoordinatorInterface;

/**
 * This service coordinates the whole flow of process.
 * Executes steps and starts/completes flows.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CoordinatorInterface
{
    /**
     * Register new process.
     *
     * @param string           $alias
     * @param ProcessInterface $process
     */
    function registerProcess($alias, ProcessInterface $process);

    /**
     * Load process with given alias.
     *
     * @param string $alias
     *
     * @return ProcessInterface
     */
    function loadProcess($alias);

    function start($processAlias);
    function display($processAlias, $stepAlias);
    function forward($processAlias, $stepAlias);
}
