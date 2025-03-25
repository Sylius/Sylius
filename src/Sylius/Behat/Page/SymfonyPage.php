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

namespace Sylius\Behat\Page;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage as BaseSymfonyPage;
use Sylius\Behat\Service\DriverHelper;

abstract class SymfonyPage extends BaseSymfonyPage implements SymfonyPageInterface
{
    protected function getElement(string $name, array $parameters = []): NodeElement
    {
        DriverHelper::waitForPageToLoad($this->getSession());

        return parent::getElement($name, $parameters);
    }
}
