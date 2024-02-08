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

namespace Sylius\Behat\Element\Admin\CatalogPromotion;

use Sylius\Component\Core\Model\ChannelInterface;

interface FilterElementInterface
{
    public function chooseChannel(ChannelInterface $channel): void;

    public function chooseEnabled(): void;

    public function chooseState(string $state): void;

    public function specifyStartDateFrom(string $date): void;

    public function specifyStartDateTo(string $date): void;

    public function specifyEndDateFrom(string $date): void;

    public function specifyEndDateTo(string $date): void;

    public function search(string $phrase): void;

    public function filter(): void;
}
