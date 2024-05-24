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

namespace Sylius\Behat\Page\Admin\ProductReview;

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    public function specifyTitle(string $title): void
    {
        $this->getElement('title')->setValue($title);
    }

    public function specifyComment(string $comment): void
    {
        $this->getElement('comment')->setValue($comment);
    }

    public function chooseRating(string $rating): void
    {
        $position = (int) $rating - 1;

        $this->getElement('rating', ['%position%' => $position])->getParent()->click();
    }

    public function getRating(): string
    {
        return $this->getElement('checked_rating')->getValue();
    }

    public function getProductName(): string
    {
        return $this->getElement('product_name')->getText();
    }

    public function getCustomerName(): string
    {
        return $this->getElement('customer_name')->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'checked_rating' => 'input[checked="checked"]',
            'comment' => '[data-test-comment]',
            'rating' => '#sylius_admin_product_review_rating_%position%',
            'customer_name' => '[data-test-author-name]',
            'product_name' => '[data-test-product-name]',
            'title' => '[data-test-title]',
        ]);
    }
}
