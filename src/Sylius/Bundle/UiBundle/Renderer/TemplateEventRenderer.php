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

final class TemplateEventRenderer implements TemplateEventRendererInterface
{
    /** @var TemplateBlockRegistryInterface */
    private $templateBlockRegistry;

    /** @var TemplateBlockRendererInterface */
    private $templateBlockRenderer;

    public function __construct(TemplateBlockRegistryInterface $templateBlockRegistry, TemplateBlockRendererInterface $templateBlockRenderer)
    {
        $this->templateBlockRegistry = $templateBlockRegistry;
        $this->templateBlockRenderer = $templateBlockRenderer;
    }

    public function render(string $eventName, array $context = []): string
    {
        $templateBlocks = $this->templateBlockRegistry->findEnabledForEvent($eventName);
        $renderedTemplates = [];

        foreach ($templateBlocks as $templateBlock) {
            $renderedTemplates[] = $this->templateBlockRenderer->render($eventName, $templateBlock, $context);
        }

        return implode("\n", $renderedTemplates);
    }
}
