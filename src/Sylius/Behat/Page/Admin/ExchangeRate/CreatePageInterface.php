<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ExchangeRate;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePage;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface CreatePageInterface extends BaseCreatePage
{
    /**
     * @param float $ratio
     */
    public function specifyRatio($ratio);

    /**
     * @param string $currency
     */
    public function chooseSourceCurrency($currency);

    /**
     * @param string $currency
     */
    public function chooseTargetCurrency($currency);

    /**
     * @param string $expectedMessage
     *
     * @return bool
     */
    public function hasFormValidationError($expectedMessage);
}
