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

namespace Sylius\Bundle\AdminBundle\Twig;

use Sylius\Bundle\AdminBundle\Grid\Renderer\GridRendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class GridExtension extends AbstractExtension
{
    public function __construct(
        private readonly GridRendererInterface $gridRenderer,
    ) {
    }

    /**
     * @return array<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_grid_render_item_action', [$this->gridRenderer, 'renderItemAction'], ['is_safe' => ['html']]),
        ];
    }
}
