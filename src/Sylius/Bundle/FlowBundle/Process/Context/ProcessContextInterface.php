<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Process\Context;

use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;
use Sylius\Bundle\FlowBundle\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface for process context.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ProcessContextInterface
{
    function setCurrent(StepInterface $current);
    function complete();

    function getStorage();
    function setStorage(StorageInterface $storage);
    function getRequest();
    function setRequest(Request $request);
}
