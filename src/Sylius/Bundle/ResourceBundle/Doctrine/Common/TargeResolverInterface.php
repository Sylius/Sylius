<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Doctrine\Common;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
interface TargeResolverInterface
{
    /**
     * @param ContainerBuilder $container
     *
     * @param array            $interfaces
     */
    public function resolve(ContainerBuilder $container, array $interfaces);
}