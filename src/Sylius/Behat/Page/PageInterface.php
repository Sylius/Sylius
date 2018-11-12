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

namespace Sylius\Behat\Page;

interface PageInterface
{
    /**
     * @throws UnexpectedPageException If page is not opened successfully
     */
    public function open(array $urlParameters = []);

    public function tryToOpen(array $urlParameters = []);

    /**
     * @throws UnexpectedPageException
     */
    public function verify(array $urlParameters = []);

    public function isOpen(array $urlParameters = []): bool;
}
