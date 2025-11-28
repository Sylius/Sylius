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

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage as BaseSymfonyPage;
use Sylius\Behat\Behaviour\WaitsForElements;

abstract class SyliusPage extends BaseSymfonyPage implements SyliusPageInterface
{
    use WaitsForElements;

    protected function blur(): void
    {
        if ($this->isJavascript() === false) {
            return;
        }

        $this->getDocument()->find('css', 'body')->click();
    }
}
