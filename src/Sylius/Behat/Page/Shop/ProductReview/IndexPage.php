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

namespace Sylius\Behat\Page\Shop\ProductReview;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class IndexPage extends SymfonyPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName(): string
    {
        return 'sylius_shop_product_review_index';
    }

    /**
     * {@inheritdoc}
     */
    public function countReviews()
    {
        return count($this->getElement('reviews')->findAll('css', '.comment'));
    }

    /**
     * {@inheritdoc}
     */
    public function hasReviewTitled($title)
    {
        return null !== $this->getElement('reviews')->find('css', sprintf('.comment:contains("%s")', $title));
    }

    /**
     * {@inheritdoc}
     */
    public function hasNoReviewsMessage()
    {
        $reviewsContainerText = $this->getElement('reviews')->getText();

        return false !== strpos($reviewsContainerText, 'There are no reviews');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'reviews' => '#sylius-product-reviews',
        ]);
    }
}
