<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Twig;

use Sylius\Bundle\SettingsBundle\Templating\Helper\SettingsHelper;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SettingsExtension extends \Twig_Extension
{
    /**
     * @var SettingsHelper
     */
    private $helper;

    /**
     * @param SettingsHelper $helper
     */
    public function __construct(SettingsHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
             new \Twig_SimpleFunction('sylius_settings_all', [$this->helper, 'getSettings']),
             new \Twig_SimpleFunction('sylius_settings_get', [$this->helper, 'getSettingsParameter']),
             new \Twig_SimpleFunction('sylius_settings_has', [$this->helper, 'hasSettingsParameter']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_settings';
    }
}
