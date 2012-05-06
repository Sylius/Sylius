<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Setup;

use Sylius\Bundle\FlowBundle\Setup\Builder\SetupBuilderInterface;
use Sylius\Bundle\FlowBundle\Setup\Step\StepInterface;

/**
 * Interface for setup object.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface SetupInterface
{
    function build(SetupBuilderInterface $builder, array $options);
    function validate($currentStepIndex);
    function countSteps();
    function getStep($index);
    function setStep($index, StepInterface $step);
    function hasStep($index);
    function getAlias();
    function setAlias($alias);
}
