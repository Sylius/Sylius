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

use Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface;

/**
 * @experimental
 */
final class DelegatingTemplateEventRenderer implements TemplateEventRendererInterface
{
    private TemplateBlockRegistryInterface $templateBlockRegistry;

    private TemplateBlockRendererInterface $templateBlockRenderer;

    public function __construct(TemplateBlockRegistryInterface $templateBlockRegistry, TemplateBlockRendererInterface $templateBlockRenderer)
    {
        $this->templateBlockRegistry = $templateBlockRegistry;
        $this->templateBlockRenderer = $templateBlockRenderer;
    }

    public function render(array $eventNames, array $context = []): string
    {
        $templateBlocks = $this->templateBlockRegistry->findEnabledForEvents($eventNames);
        $renderedTemplates = [];

        foreach ($templateBlocks as $templateBlock) {
            $renderedTemplates[] = $this->templateBlockRenderer->render($templateBlock, $context);
        }

        return implode("\n", $renderedTemplates);
    }
}
