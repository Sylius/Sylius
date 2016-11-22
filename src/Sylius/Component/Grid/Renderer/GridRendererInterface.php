<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Renderer;

use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\View\GridViewInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface GridRendererInterface
{
    /**
     * @param GridViewInterface $gridView
     * @param string|null $template
     *
     * @return mixed
     */
    public function render(GridViewInterface $gridView, $template = null);

    /**
     * @param GridViewInterface $gridView
     * @param Field $field
     * @param mixed $data
     *
     * @return mixed
     */
    public function renderField(GridViewInterface $gridView, Field $field, $data);

    /**
     * @param GridViewInterface $gridView
     * @param Action $action
     * @param mixed|null $data
     *
     * @return mixed
     */
    public function renderAction(GridViewInterface $gridView, Action $action, $data = null);

    /**
     * @param GridViewInterface $gridView
     * @param Filter $filter
     *
     * @return mixed
     */
    public function renderFilter(GridViewInterface $gridView, Filter $filter);
}
