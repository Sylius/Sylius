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

namespace Sylius\Behat\Page\Shop\ProductReview;

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;

interface CreatePageInterface extends PageInterface
{
    public function titleReview(?string $title): void;

    public function setComment(?string $comment): void;

    public function setAuthor(string $author): void;

    public function rateReview(int $rate): void;

    public function submitReview(): void;

    public function getRateValidationMessage(): string;

    public function getTitleValidationMessage(): string;

    public function getCommentValidationMessage(): string;

    public function getAuthorValidationMessage(): string;
}
