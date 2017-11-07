<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Renderer;

use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\View\GridViewInterface;

interface BulkActionGridRendererInterface
{
    /**
     * @param GridViewInterface $gridView
     * @param Action $bulkAction
     * @param mixed|null $data
     *
     * @return mixed
     */
    public function renderBulkAction(GridViewInterface $gridView, Action $bulkAction, $data = null): string;
}
