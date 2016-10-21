<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\ProductReview;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class IndexPage extends SymfonyPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_product_reviews_index';
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
    public function hasNoReviewMessage()
    {
        return 'There are no reviews' === $this->getElement('reviews')->find('css', '.comments')->getText();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'reviews' => '#reviews',
        ]);
    }
}
