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

namespace Sylius\Behat\Element;

use FriendsOfBehat\PageObjectExtension\Element\Element as BaseElement;
use Sylius\Behat\Behaviour\WaitsForElements;

abstract class SyliusElement extends BaseElement
{
    use WaitsForElements;

    /**
     * Override getDocument() to return our DocumentElement wrapper.
     */
    protected function getDocument(): DocumentElement
    {
        $baseDocument = parent::getDocument();

        return new DocumentElement($this->getSession());
    }
}
