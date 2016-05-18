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

use Sylius\Behat\Page\SymfonyPageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface GeneratePageInterface extends SymfonyPageInterface
{
    /**
     * @param string $message
     *
     * @return bool
     */
    public function checkAmountValidation($message);

    /**
     * @param string $message
     *
     * @return bool
     */
    public function checkCodeLengthValidation($message);

    /**
     * @param string $message
     *
     * @return bool
     */
    public function checkGenerationValidation($message);

    public function generate();

    /**
     * @param int $amount
     */
    public function specifyAmount($amount);

    /**
     * @param int $codeLength
     */
    public function specifyCodeLength($codeLength);

    /**
     * @param \DateTime $date
     */
    public function setExpiresAt(\DateTime $date);

    /**
     * @param int $limit
     */
    public function setUsageLimit($limit);
}
