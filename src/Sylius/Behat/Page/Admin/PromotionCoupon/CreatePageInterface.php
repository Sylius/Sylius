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

namespace Sylius\Behat\Page\Admin\PromotionCoupon;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param int $limit
     */
    public function setCustomerUsageLimit($limit);

    /**
     * @param \DateTimeInterface $date
     */
    public function setExpiresAt(\DateTimeInterface $date);

    /**
     * @param string $code
     */
    public function specifyCode($code);

    /**
     * @param int $limit
     */
    public function setUsageLimit($limit);
}
