<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Twig;

use Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelper;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LocaleExtension extends \Twig_Extension
{
    /**
     * @var LocaleHelper
     */
    protected $helper;

    /**
     * @param LocaleHelper $helper
     */
    public function __construct(LocaleHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('sylius_locale', [$this, 'getCurrentLocale']),
        ];
    }

    /**
     * @return string
     */
    public function getCurrentLocale()
    {
        return $this->helper->getCurrentLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_locale';
    }
}
