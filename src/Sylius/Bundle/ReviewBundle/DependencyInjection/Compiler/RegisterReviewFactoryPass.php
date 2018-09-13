<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ReviewBundle\DependencyInjection\Compiler;

use Sylius\Component\Review\Factory\ReviewFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class RegisterReviewFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getParameter('sylius.review.subjects') as $subject => $configuration) {
            $factory = $container->findDefinition('sylius.factory.' . $subject . '_review');

            $reviewFactoryDefinition = new Definition(ReviewFactory::class, [$factory]);
            $reviewFactoryDefinition->setPublic(true);

            $container->setDefinition(sprintf('sylius.factory.' . $subject . '_review'), $reviewFactoryDefinition);
        }
    }
}
