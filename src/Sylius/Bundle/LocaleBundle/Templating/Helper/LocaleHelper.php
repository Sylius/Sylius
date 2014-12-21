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
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LocaleHelper extends Helper
{
    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    public function __construct(LocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;
    }

    /**
     * Get currently used locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->localeContext->getLocale();
    }

    /**
     * Get currently used calendar system.
     *
     * @return string
     */
    public function getCalendar()
    {
        return $this->localeContext->getCalendar();
    }

    /**
     * Get currently used language direction.
     *
     * @return string
     */
    public function getDirection()
    {
        return $this->localeContext->getDirection();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_locale';
    }
}
