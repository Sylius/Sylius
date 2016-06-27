<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service\Mocker;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface PaypalApiMockerInterface
{
    public function mockApiSuccessfulPaymentResponse();

    public function mockApiPaymentInitializeResponse();

    public function unmockAllServices();
}
