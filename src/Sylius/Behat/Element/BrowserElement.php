<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Element;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class BrowserElement extends Element implements BrowserElementInterface
{
    public function goBack(): void
    {
        $this->getDriver()->back();
    }
}
