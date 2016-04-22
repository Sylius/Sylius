<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Promotion;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @param string $name
     */
    public function nameIt($name);

    /**
     * @param string $channelName
     *
     * @return bool
     */
    public function checkChannelsState($channelName);

    /**
     * @return bool
     */
    public function isCodeDisabled();

    /**
     * @param string $limit
     */
    public function fillUsageLimit($limit);

    public function makeExclusive();

    public function checkCouponBased();

    /**
     * @param string $name
     */
    public function checkChannel($name);
}
