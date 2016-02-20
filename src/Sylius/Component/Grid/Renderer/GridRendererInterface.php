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
use Sylius\Component\Grid\View\GridView;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface GridRendererInterface
{
    /**
     * @param GridView $gridView
     * @param string $template
     *
     * @return mixed
     */
    public function render(GridView $gridView, $template = null);

    /**
     * @param GridView $gridView
     * @param Field $field
     * @param mixed $data
     *
     * @return mixed
     */
    public function renderField(GridView $gridView, Field $field, $data);

    /**
     * @param GridView $gridView
     * @param Action $action
     * @param mixed $data
     *
     * @return mixed
     */
    public function renderAction(GridView $gridView, Action $action, $data = null);

    /**
     * @param GridView $gridView
     * @param Filter $filter
     *
     * @return mixed
     */
    public function renderFilter(GridView $gridView, Filter $filter);
}
