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

namespace Sylius\Bundle\UiBundle\Twig;

use Sylius\Bundle\UiBundle\Renderer\TemplateEventRendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TemplateEventExtension extends AbstractExtension
{
    /** @var TemplateEventRendererInterface */
    private $templateEventRenderer;

    public function __construct(TemplateEventRendererInterface $templateEventRenderer)
    {
        $this->templateEventRenderer = $templateEventRenderer;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_template_event', [$this->templateEventRenderer, 'render'], ['is_safe' => ['html']]),
        ];
    }
}
