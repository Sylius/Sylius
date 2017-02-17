<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Twig;

use Sylius\Bundle\GridBundle\Templating\Helper\GridHelper;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class GridExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('sylius_grid_render', [GridHelper::class, 'renderGrid'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('sylius_grid_render_field', [GridHelper::class, 'renderField'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('sylius_grid_render_action', [GridHelper::class, 'renderAction'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('sylius_grid_render_filter', [GridHelper::class, 'renderFilter'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sylius_grid';
    }
}
