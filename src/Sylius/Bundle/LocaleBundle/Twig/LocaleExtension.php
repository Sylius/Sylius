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
use Symfony\Component\Intl\Intl;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Aram Alipoor <aram.alipoor@gmail.com>
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
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter(
                'sylius_localized_date', [$this, 'filterLocalizedDate'], ['needs_environment' => true]
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('sylius_locale', [$this, 'getCurrentLocale']),
            new \Twig_SimpleFunction('sylius_direction', [$this, 'getCurrentDirection']),
            new \Twig_SimpleFunction('sylius_calendar', [$this, 'getCurrentCalendar']),
            new \Twig_SimpleFunction('sylius_language', [$this, 'getLanguage']),
            new \Twig_SimpleFunction('sylius_rtl', [$this, 'isRtl']),
        ];
    }

    /**
     * Returns translated language and country name of a locale.
     *
     * @param string|null $locale If not passed will use current locale.
     *
     * @return string
     */
    public function getLanguage($locale = null)
    {
        if (null === $locale) {
            $locale = $this->helper->getCurrentLocale();
        }

        return Intl::getLocaleBundle()->getLocaleName($locale);
    }

    /**
     * @return string
     */
    public function getCurrentCalendar()
    {
        return $this->helper->getCurrentCalendar();
    }

    /**
     * @return string
     */
    public function getCurrentLocale()
    {
        return $this->helper->getCurrentLocale();
    }

    /**
     * @return string
     */
    public function getCurrentDirection()
    {
        return $this->helper->getCurrentDirection();
    }

    /**
     * @return bool True if language direction is RTL
     */
    public function isRtl()
    {
        return 'rtl' === $this->helper->getCurrentDirection();
    }

    /**
     * Filter to format date to a localized calendar-aware date.
     *
     * @param \Twig_Environment $env
     * @param string $date PHP-compatible date format
     * @param string $timezone
     * @param string $format
     *
     * @return bool|string
     *
     * @throws \Twig_Error_Runtime
     */
    public function filterLocalizedDate(\Twig_Environment $env, $date, $format = null, $timezone = null)
    {
        if (null === $format) {
            $formats = $env->getExtension('core')->getDateFormat();
            $format = $date instanceof \DateInterval ? $formats[1] : $formats[0];
        }

        $timezone = $timezone ?: $env->getExtension('core')->getTimezone();

        return $this->helper->formatDate($date, $format, $timezone);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_locale';
    }
}
