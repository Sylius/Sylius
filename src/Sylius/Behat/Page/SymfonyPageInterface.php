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

interface SymfonyPageInterface extends PageInterface
{
    public function getRouteName(): string;

    /**
     * @throws UnexpectedPageException
     */
    public function verifyRoute(array $requiredUrlParameters = []);
}
