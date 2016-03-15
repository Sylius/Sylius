<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Sitemap\Model;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ChangeFrequency
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $changeFrequency
     */
    private function __construct($changeFrequency)
    {
        $this->value = $changeFrequency;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * @return ChangeFrequency
     */
    public static function always()
    {
        return new self('always');
    }

    /**
     * @return ChangeFrequency
     */
    public static function hourly()
    {
        return new self('hourly');
    }

    /**
     * @return ChangeFrequency
     */
    public static function daily()
    {
        return new self('daily');
    }

    /**
     * @return ChangeFrequency
     */
    public static function weekly()
    {
        return new self('weekly');
    }

    /**
     * @return ChangeFrequency
     */
    public static function monthly()
    {
        return new self('monthly');
    }

    /**
     * @return ChangeFrequency
     */
    public static function yearly()
    {
        return new self('yearly');
    }

    /**
     * @return ChangeFrequency
     */
    public static function never()
    {
        return new self('never');
    }
}
