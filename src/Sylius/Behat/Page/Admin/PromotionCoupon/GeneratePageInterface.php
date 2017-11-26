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

use Sylius\Behat\Page\SymfonyPageInterface;

interface GeneratePageInterface extends SymfonyPageInterface
{
    /**
     * @param string $message
     *
     * @return bool
     */
    public function checkAmountValidation(string $message): bool;

    /**
     * @param string $message
     *
     * @return bool
     */
    public function checkCodeLengthValidation(string $message): bool;

    /**
     * @param string $message
     *
     * @return bool
     */
    public function checkGenerationValidation(string $message): bool;

    public function generate(): void;

    /**
     * @param int $amount
     */
    public function specifyAmount(int $amount): void;

    /**
     * @param int $codeLength
     */
    public function specifyCodeLength(int $codeLength): void;

    /**
     * @param \DateTimeInterface $date
     */
    public function setExpiresAt(\DateTimeInterface $date): void;

    /**
     * @param int $limit
     */
    public function setUsageLimit(int $limit): void;
}
