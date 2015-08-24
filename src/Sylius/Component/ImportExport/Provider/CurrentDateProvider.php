<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport\Provider;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CurrentDateProvider implements CurrentDateProviderInterface
{
    /**
     * @var string
     */
    private $timezone;

    /**
     * @param string $timezone
     */
    public function __construct($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentDate()
    {
        $date = new \DateTime('now', new \DateTimeZone($this->timezone));

        return $date;
    }
}
