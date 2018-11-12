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

namespace Sylius\Behat\Page\Admin\ProductVariant;

use Sylius\Behat\Page\SymfonyPageInterface;

interface GeneratePageInterface extends SymfonyPageInterface
{
    public function generate();

    public function specifyPrice(int $nth, int $price, string $channelName);

    public function specifyCode(int $nth, string $code);

    public function removeVariant(int $nth);

    public function getValidationMessage(string $element, int $position): string;

    public function getPricesValidationMessage(string $position): string;
}
