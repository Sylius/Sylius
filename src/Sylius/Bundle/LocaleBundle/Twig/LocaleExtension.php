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
final class LocaleExtension extends \Twig_Extension
{
    /**
     * @var LocaleHelperInterface
     */
    private $localeHelper;

    /**
     * @param LocaleHelperInterface $localeHelper
     */
    public function __construct(LocaleHelperInterface $localeHelper)
    {
        $this->localeHelper = $localeHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sylius_locale_name', [$this->localeHelper, 'convertCodeToName']),
        ];
    }
}
