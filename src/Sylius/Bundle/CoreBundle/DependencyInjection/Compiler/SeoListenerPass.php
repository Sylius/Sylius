<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SeoListenerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('sylius.listener.seo') || !$container->hasParameter('sylius.seo.formulas')) {
            return;
        }

        $definition = $container->getDefinition('sylius.listener.seo');

        $seo = array();
        foreach ($container->getParameter('sylius.seo.formulas') as $name => $data) {
            $seo[$data['class']] = $data['formulas'];

            $definition->addTag('kernel.event_listener', array('event' => sprintf('sylius.%s.pre_show', $name), 'method' => 'preShow'));
            $definition->addTag('kernel.event_listener', array('event' => sprintf('sylius.%s.post_update', $name), 'method' => 'postUpdate'));
            $definition->addTag('kernel.event_listener', array('event' => sprintf('sylius.%s.pre_delete', $name), 'method' => 'preDelete'));
        }

        $definition->replaceArgument(2, $seo);
    }
}
