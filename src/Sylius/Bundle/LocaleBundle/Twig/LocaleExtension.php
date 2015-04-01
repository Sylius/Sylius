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
 * Sylius locale Twig helper.
 *
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
        return array(
            new \Twig_SimpleFilter('sylius_localized_date', array($this, 'dateFilter'),
                array('needs_environment' => true)),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('sylius_locale', array($this, 'getLocale')),
            new \Twig_SimpleFunction('sylius_language', array($this, 'getLanguage')),
            new \Twig_SimpleFunction('sylius_direction', array($this, 'getDirection')),
            new \Twig_SimpleFunction('sylius_calendar', array($this, 'getCalendar')),
            new \Twig_SimpleFunction('sylius_rtl', array($this, 'isRtl')),
        );
    }

    /**
     * Returns localized language name from context or passed locale code
     *
     * @param string|null $code
     *
     * @return string
     */
    public function getLanguage($code = null)
    {
        if ($code === null) {
            $code = $this->helper->getLocale();
        }

        return Intl::getLocaleBundle()->getLocaleName($code);
    }

    /**
     * Returns currently active calendar system
     *
     * @return string Possible values are `gregorian`, `persian`, `islamic`, `hebrew`, etc.
     */
    public function getCalendar()
    {
        return $this->helper->getCalendar();
    }

    /**
     * Returns currectly active locale
     *
     * @return string Locale code
     */
    public function getLocale()
    {
        return $this->helper->getLocale();
    }

    /**
     * Returns current language direction
     *
     * @return string Possible values are `rtl` and `ltr`
     */
    public function getDirection()
    {
        return $this->helper->getDirection();
    }

    /**
     * Checks whether if current language direction is RTL or not.
     *
     * @return bool True if language direction is RTL
     */
    public function isRtl()
    {
        return $this->helper->getDirection() === 'rtl';
    }

    /**
     * Twig filter to format date in a calendar-aware fashion
     *
     * @param \Twig_Environment $env
     * @param $date
     * @param $timezone
     * @param $format
     *
     * @return bool|string
     * @throws \Twig_Error_Runtime
     */
    public function dateFilter(\Twig_Environment $env, $date, $format = null, $timezone = null)
    {
        if (null === $format) {
            $formats = $env->getExtension('core')->getDateFormat();
            $format = $date instanceof \DateInterval ? $formats[1] : $formats[0];
        }

        // Determine the timezone
        if (!$timezone) {
            $timezone = $env->getExtension('core')->getTimezone();
        }

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