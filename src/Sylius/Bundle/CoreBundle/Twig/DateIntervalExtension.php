<?php

namespace Sylius\Bundle\CoreBundle\Twig;

class DateIntervalExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('sylius_interval', array($this, 'formatDateInterval'))
        );
    }

    /**
     * Formats a date interval using all available properties.
     *
     * @param \DateInterval $interval
     *
     * @return string
     */
    public function formatDateInterval(\DateInterval $interval)
    {
        $format = '';

        if ($interval->y) {
            $format .= '%y years, ';
        }
        if ($interval->m) {
            $format .= '%m months, ';
        }
        if ($interval->d) {
            $format .= '%d days, ';
        }
        if ($interval->h) {
            $format .= '%h hours, ';
        }
        if ($interval->i) {
            $format .= '%i minutes, ';
        }
        if ($interval->s) {
            $format .= '%s seconds, ';
        }

        return $interval->format(trim($format, ', '));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_interval';
    }
}
