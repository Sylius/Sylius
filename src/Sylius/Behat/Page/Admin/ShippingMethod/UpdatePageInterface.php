<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ShippingMethod;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @return bool
     */
    public function isCodeDisabled();

    /**
     * @param string $channelName
     *
     * @return bool
     */
    public function isAvailableInChannel($channelName);

    public function enable();

    public function disable();

    public function removeZone();

    /**
     * @param string $channelCode
     *
     * @return string
     *
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForAmount($channelCode);
}
