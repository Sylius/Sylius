<?php

declare(strict_types=1);

namespace Sylius\Behat\Service;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Driver\Selenium2Driver;
use DMore\ChromeDriver\ChromeDriver;

abstract class DriverHelper
{
    /**
     * @param DriverInterface $driver
     *
     * @return bool
     */
    public static function supportsJavascript(DriverInterface $driver)
    {
        return $driver instanceof Selenium2Driver || $driver instanceof ChromeDriver;
    }
}
