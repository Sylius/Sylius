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

namespace Sylius\Bundle\AdminBundle\Grid\Renderer;

use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Renderer\GridRendererInterface as BaseGridRendererInterface;
use Sylius\Component\Grid\View\GridViewInterface;

interface GridRendererInterface extends BaseGridRendererInterface
{
    public function renderItemAction(GridViewInterface $gridView, Action $action, mixed $data = null): string;
}
