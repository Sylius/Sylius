<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Process\Step;

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;

interface StepInterface
{
    function getId();
    function setId($id);

    function display(ProcessContextInterface $context);
    function forward(ProcessContextInterface $context);

    function isActive();
}
