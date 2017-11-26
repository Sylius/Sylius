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
    public function generate(): void;

    /**
     * @param int $nth
     * @param int $price
     * @param string $channelName
     */
    public function specifyPrice(int $nth, int $price, string $channelName): void;

    /**
     * @param int $nth
     * @param string $code
     */
    public function specifyCode(int $nth, string $code): void;

    /**
     * @param int $nth
     */
    public function removeVariant(int $nth): void;

    /**
     * @param string $element
     * @param int $position
     *
     * @return string
     */
    public function getValidationMessage(string $element, int $position): string;

    /**
     * @param string $position
     *
     * @return string
     */
    public function getPricesValidationMessage(string $position): string;
}
