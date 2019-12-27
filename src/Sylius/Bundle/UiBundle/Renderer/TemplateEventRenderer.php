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
use Twig\Environment;

final class TemplateEventRenderer implements TemplateEventRendererInterface
{
    /** @var TemplateBlockRegistryInterface */
    private $templateBlockRegistry;

    /** @var Environment */
    private $twig;

    /** @var bool */
    private $debug;

    public function __construct(TemplateBlockRegistryInterface $templateBlockRegistry, Environment $twig, bool $debug)
    {
        $this->templateBlockRegistry = $templateBlockRegistry;
        $this->twig = $twig;
        $this->debug = $debug;
    }

    public function render(string $eventName, array $context = []): string
    {
        $templateBlocks = $this->templateBlockRegistry->findEnabledForEvent($eventName);
        $renderedTemplates = [];

        foreach ($templateBlocks as $templateBlock) {
            if ($this->debug && strrpos($templateBlock->template(), '.html.twig') !== false) {
                $renderedTemplates[] = sprintf(
                    '<!-- event name: "%s", block name: "%s", template: "%s", priority: %d -->',
                    $eventName,
                    $templateBlock->name(),
                    $templateBlock->template(),
                    $templateBlock->priority()
                );
            }
            $renderedTemplates[] = $this->twig->render($templateBlock->template(), $context);
        }

        return implode("\n", $renderedTemplates);
    }
}
