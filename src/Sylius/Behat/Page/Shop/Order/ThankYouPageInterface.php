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

namespace Sylius\Behat\Page\Shop\Order;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

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
