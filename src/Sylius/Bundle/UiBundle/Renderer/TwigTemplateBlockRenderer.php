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

/**
 * @experimental
 */
final class TwigTemplateBlockRenderer implements TemplateBlockRendererInterface
{
    public function __construct(private Environment $twig)
    {
    }

    public function render(TemplateBlock $templateBlock, array $context = []): string
    {
        return $this->twig->render(
            $templateBlock->getTemplate(),
            array_replace($templateBlock->getContext(), $context),
        );
    }
}
