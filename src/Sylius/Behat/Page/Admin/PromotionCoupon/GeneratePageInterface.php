<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\PromotionCoupon;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface GeneratePageInterface extends SymfonyPageInterface
{
    public function checkAmountValidation(string $message): bool;

    public function checkCodeLengthValidation(string $message): bool;

    public function checkGenerationValidation(string $message): bool;

    public function generate(): void;

    public function specifyAmount(?int $amount): void;

    public function specifyCodeLength(?int $codeLength): void;

    public function setExpiresAt(\DateTimeInterface $date): void;

    public function setUsageLimit(int $limit): void;

    public function specifyPrefix(string $prefix): void;

    public function specifySuffix(string $suffix): void;
}
