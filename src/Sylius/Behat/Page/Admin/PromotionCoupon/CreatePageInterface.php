<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\PromotionCoupon;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param int $limit
     */
    public function setCustomerUsageLimit($limit);

    /**
     * @param \DateTime $date
     */
    public function setExpiresAt(\DateTime $date);

    /**
     * @param string $code
     */
    public function specifyCode($code);

    /**
     * @param int $limit
     */
    public function setUsageLimit($limit);
}
