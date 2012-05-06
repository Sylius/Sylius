<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Setup\Builder;

use Sylius\Bundle\FlowBundle\Setup\SetupInterface;
use Sylius\Bundle\FlowBundle\Setup\Step\StepInterface;

interface SetupBuilderInterface
{
    function build(SetupInterface $setup, array $options = array());
    function addStep($step);
    function removeStep($step);
    function registerSetup($alias, SetupInterface $setup);
    function loadSetup($alias);
    function registerStep($alias, StepInterface $step);
    function loadStep($alias);
}
