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

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @return string
     */
    public function getRatio();

    /**
     * @param string $ratio
     */
    public function changeRatio($ratio);

    /**
     * @return bool
     */
    public function isSourceCurrencyDisabled();

    /**
     * @return bool
     */
    public function isTargetCurrencyDisabled();
}
