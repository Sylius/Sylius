<?php

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
     * Constructor.
     *
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
        $date = new \DateTime('now');
        $timezone = new \DateTimeZone($this->timezone);
        $date->setTimezone($timezone);

        return $date;
    }
}
