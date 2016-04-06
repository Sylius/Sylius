<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Templating\Helper;

use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class LocaleHelper extends Helper implements LocaleHelperInterface
{
    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(LocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentLocale()
    {
        return $this->localeContext->getCurrentLocale();
    }

    /**
     * @param string $localeCode
     *
     * @return string|null
     */
    public function convertToName($localeCode)
    {
        return Intl::getLocaleBundle()->getLocaleName($localeCode, $this->getCurrentLocale());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_locale';
    }
}
