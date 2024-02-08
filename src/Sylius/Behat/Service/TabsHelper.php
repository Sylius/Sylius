<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Service;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use DMore\ChromeDriver\ChromeDriver;

abstract class TabsHelper
{
    public static function switchTab(Session $session, NodeElement $tabsContainer, string $dataTabHook): void
    {
        $driver = $session->getDriver();
        if (false === $driver instanceof ChromeDriver && false === $driver instanceof Selenium2Driver) {
            return;
        }

        $tab = $tabsContainer->find('css', sprintf('.item[data-tab*="%s"]', $dataTabHook));
        if ($tab->hasClass('active')) {
            return;
        }

        $tab->click();

        $tabContent = $tabsContainer->find('css', sprintf('.tab[data-tab*="%s"]', $dataTabHook));

        $session->getPage()->waitFor(5, fn () => $tabContent->isVisible());
    }
}
