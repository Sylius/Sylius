<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\LocaleBundle\Twig;

use Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelperInterface;

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
    public function getFilters(): array
    {
        return [
            new \Twig_Filter('sylius_locale_name', [$this->localeHelper, 'convertCodeToName']),
        ];
    }
}
