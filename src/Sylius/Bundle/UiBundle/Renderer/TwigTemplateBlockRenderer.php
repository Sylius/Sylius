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

namespace Sylius\Bundle\UiBundle\Renderer;

use Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;
use Webmozart\Assert\Assert;

/**
 * @experimental
 */
final class TwigTemplateBlockRenderer implements TemplateBlockRendererInterface
{
    public function __construct(private Environment $twig, private ContainerInterface $container)
    {
    }

    public function render(TemplateBlock $templateBlock, array $context = []): string
    {
        $contextProvider = $this->container->get($templateBlock->getContextProviderClass());
        Assert::isInstanceOf($contextProvider, ContextProviderInterface::class);

        return $this->twig->render(
            $templateBlock->getTemplate(),
            $contextProvider->provide($context, $templateBlock->getContext()),
        );
    }
}
