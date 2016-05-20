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

use Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelperInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class LocaleExtension extends \Twig_Extension
{
    /**
     * @var LocaleHelperInterface
     */
    protected $localeHelper;

    /**
     * @param LocaleHelperInterface $localeHelper
     */
    public function __construct(
        LocaleHelperInterface $localeHelper
    ) {
        $this->localeHelper = $localeHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('sylius_locale', [$this->localeHelper, 'getCurrentLocale']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sylius_locale_name', [$this->localeHelper, 'convertToName']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_locale';
    }
}
