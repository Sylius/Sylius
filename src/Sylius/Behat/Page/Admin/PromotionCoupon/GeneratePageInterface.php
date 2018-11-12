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
    public function checkAmountValidation(string $message): bool;

    public function checkCodeLengthValidation(string $message): bool;

    public function checkGenerationValidation(string $message): bool;

    public function generate();

    public function specifyAmount(int $amount);

    public function specifyCodeLength(int $codeLength);

    public function setExpiresAt(\DateTimeInterface $date);

    public function setUsageLimit(int $limit);
}
