<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ProductVariant;

use Sylius\Behat\Page\SymfonyPageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface GeneratePageInterface extends SymfonyPageInterface
{
    public function generate();

    /**
     * @param int $nth
     * @param int $price
     * @param string $channelName
     */
    public function specifyPrice($nth, $price, $channelName);

    /**
     * @param int $nth
     * @param string $code
     */
    public function specifyCode($nth, $code);

    /**
     * @param int $nth
     */
    public function removeVariant($nth);

    /**
     * @param string $element
     * @param int $position
     *
     * @return string
     */
    public function getValidationMessage($element, $position);

    /**
     * @param string $position
     *
     * @return string
     */
    public function getPricesValidationMessage($position);
}
