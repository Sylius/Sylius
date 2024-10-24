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

interface IndexPageInterface extends PageInterface
{
    public function countReviews(): int;

    public function hasReviewTitled(string $title): bool;

    public function hasNoReviewsMessage(): bool;
}
