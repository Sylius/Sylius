<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\Renderer;

use Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface;

trigger_deprecation(
    'sylius/ui-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0',
    DelegatingTemplateEventRenderer::class,
);
final class DelegatingTemplateEventRenderer implements TemplateEventRendererInterface
{
    public function __construct(private TemplateBlockRegistryInterface $templateBlockRegistry, private TemplateBlockRendererInterface $templateBlockRenderer)
    {
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
