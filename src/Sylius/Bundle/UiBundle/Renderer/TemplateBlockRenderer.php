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

use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Twig\Environment;

final class TemplateBlockRenderer implements TemplateBlockRendererInterface
{
    /** @var Environment */
    private $twig;

    /** @var bool */
    private $debug;

    public function __construct(Environment $twig, bool $debug)
    {
        $this->twig = $twig;
        $this->debug = $debug;
    }

    public function render(string $eventName, TemplateBlock $templateBlock, array $context = []): string
    {
        $renderedBlock = '';

        if ($this->debug && strrpos($templateBlock->template(), '.html.twig') !== false) {
            $renderedBlock .= sprintf(
                '<!-- event name: "%s", block name: "%s", template: "%s", priority: %d -->',
                $eventName,
                $templateBlock->name(),
                $templateBlock->template(),
                $templateBlock->priority()
            );
            $renderedBlock .= "\n";
        }

        $renderedBlock .= $this->twig->render($templateBlock->template(), $context);

        return $renderedBlock;
    }
}
