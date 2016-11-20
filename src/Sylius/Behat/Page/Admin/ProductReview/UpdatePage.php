<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ProductReview;

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function specifyTitle($title)
    {
        $this->getElement('title')->setValue($title);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyComment($comment)
    {
        $this->getElement('comment')->setValue($comment);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseRating($rating)
    {
        $position = (int) $rating - 1;

        $this->getElement('rating', ['%position%' => $position])->getParent()->click();
    }

    /**
     * {@inheritdoc}
     */
    public function getRating()
    {
        return $this->getElement('checked_rating')->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductName()
    {
        return $this->getElement('product_name')->getHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerName()
    {
        return $this->getElement('customer_name')->getHtml();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'checked_rating' => 'input[checked="checked"]',
            'comment' => '#sylius_product_review_comment',
            'rating' => '#sylius_product_review_rating_%position%',
            'customer_name' => '.sylius-customer-name',
            'product_name' => '.sylius-product-name',
            'title' => '#sylius_product_review_title',
        ]);
    }
}
