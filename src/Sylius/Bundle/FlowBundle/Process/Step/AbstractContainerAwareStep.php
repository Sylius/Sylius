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

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Container aware step.
 * Builder will automatically set the container on this step.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
abstract class AbstractContainerAwareStep extends AbstractStep implements ContainerAwareInterface
{
    use ContainerAwareTrait;
}
