<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UiBundle\View\Template;

use Pagerfanta\View\Template\Template;

/**
 * @author Loïc Frémont <loic@mobizel.com>
 */
class SemanticUiTemplate extends Template
{
    static protected $defaultOptions = array(
        'prev_message'        => '&larr; Previous',
        'next_message'        => 'Next &rarr;',
        'dots_message'        => '&hellip;',
        'active_suffix'       => '',
        'css_container_class' => 'ui stackable fluid pagination menu',
        'css_item_class'      => 'item',
        'css_prev_class'      => 'prev',
        'css_next_class'      => 'next',
        'css_disabled_class'  => 'disabled',
        'css_dots_class'      => 'disabled',
        'css_active_class'    => 'active',
    );

    public function container()
    {
        return sprintf('<div class="%s">%%pages%%</div>',
            $this->option('css_container_class')
        );
    }

    public function page($page)
    {
        $text = $page;

        return $this->pageWithText($page, $text);
    }

    public function pageWithText($page, $text)
    {
        $class = null;

        return $this->pageWithTextAndClass($page, $text, $class);
    }

    private function pageWithTextAndClass($page, $text, $class)
    {
        $href = $this->generateRoute($page);

        return $this->link($class, $href, $text);
    }

    public function previousDisabled()
    {
        $class = $this->previousDisabledClass();
        $text = $this->option('prev_message');

        return $this->div($class, $text);
    }

    private function previousDisabledClass()
    {
        return $this->option('css_prev_class').' '.$this->option('css_disabled_class');
    }

    public function previousEnabled($page)
    {
        $text = $this->option('prev_message');
        $class = $this->option('css_prev_class');

        return $this->pageWithTextAndClass($page, $text, $class);
    }

    public function nextDisabled()
    {
        $class = $this->nextDisabledClass();
        $text = $this->option('next_message');

        return $this->div($class, $text);
    }

    private function nextDisabledClass()
    {
        return $this->option('css_next_class').' '.$this->option('css_disabled_class');
    }

    public function nextEnabled($page)
    {
        $text = $this->option('next_message');
        $class = $this->option('css_next_class');

        return $this->pageWithTextAndClass($page, $text, $class);
    }

    public function first()
    {
        return $this->page(1);
    }

    public function last($page)
    {
        return $this->page($page);
    }

    public function current($page)
    {
        $text = trim($page.' '.$this->option('active_suffix'));
        $class = $this->option('css_active_class');

        return $this->div($class, $text);
    }

    public function separator()
    {
        $class = $this->option('css_dots_class');
        $text = $this->option('dots_message');

        return $this->div($class, $text);
    }

    private function link($class, $href, $text)
    {
        $item_class = $this->option('css_item_class');

        return sprintf('<a class="%s %s" href="%s">%s</a>', $item_class, $class, $href, $text);
    }

    private function div($class, $text)
    {
        $item_class = $this->option('css_item_class');

        return sprintf('<div class="%s %s">%s</div>', $item_class, $class, $text);
    }
}
