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

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class IndexPage extends SymfonyPage implements IndexPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_product_review_index';
    }

    public function countReviews(): int
    {
        return count($this->getElement('reviews')->findAll('css', '.comment'));
    }

    public function hasReviewTitled(string $title): bool
    {
        return $this->hasElement('title', ['%title%' => $title]);
    }

    public function hasNoReviewsMessage(): bool
    {
        $reviewsContainerText = $this->getElement('reviews')->getText();

        return str_contains($reviewsContainerText, 'There are no reviews');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'reviews' => '[data-test-product-reviews]',
            'title' => '[data-test-product-reviews] [data-test-title="%title%"]',
        ]);
    }
}
