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

namespace Sylius\Behat\Element\Admin\Account;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class ResetElement extends Element implements ResetElementInterface
{
    public function reset(): void
    {
        $this->getElement('reset')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'reset' => 'button[type="submit"]:contains("Reset")',
        ]);
    }
}
