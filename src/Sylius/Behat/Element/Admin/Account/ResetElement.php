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

namespace Sylius\Behat\Element\Admin\Account;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class ResetElement extends Element implements ResetElementInterface
{
    public function reset(): void
    {
        $this->getDocument()->find('css', 'button[type="submit"]:contains("Reset")')->click();
    }
}
