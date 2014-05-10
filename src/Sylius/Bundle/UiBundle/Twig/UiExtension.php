<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UiBundle\Twig;

use Sylius\Bundle\UiBundle\Templating\Helper\UiHelper;

/**
 * Sylius pricing Twig helper.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class UiExtension extends \Twig_Extension
{
    /**
     * Templating helper.
     *
     * @var UiHelper
     */
    protected $helper;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            // new \Twig_SimpleFunction('sylius_ui_sort', array($this, 'renderSort'), array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_ui';
    }
}
