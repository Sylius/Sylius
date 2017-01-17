<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\DependencyInjection\Compiler;

use Sylius\Component\Review\Factory\ReviewFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class RegisterReviewFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getParameter('sylius.review.subjects') as $subject => $configuration) {
            $factory = $container->findDefinition('sylius.factory.'.$subject.'_review');
            $reviewFactoryDefinition = new Definition(ReviewFactory::class);

            $reviewFactory = $container->setDefinition(sprintf('sylius.factory.'.$subject.'_review'), $reviewFactoryDefinition);
            $reviewFactory->addArgument($factory);
        }
    }
}
