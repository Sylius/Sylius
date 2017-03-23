<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Order;

use Sylius\Behat\Page\SymfonyPageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ThankYouPageInterface extends SymfonyPageInterface
{
    public function goToOrderDetails();

    /**
     * @return bool
     */
    public function hasThankYouMessage();

    /**
     * @return string
     */
    public function getInstructions();

    /**
     * @return bool
     */
    public function hasInstructions();

    /**
     * @return bool
     */
    public function hasChangePaymentMethodButton();
}
