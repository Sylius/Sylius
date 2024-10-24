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
        $this->getElement('rating', ['%value%' => $rating])->getParent()->click();
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
        return $this->getElement('author_name')->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'author_name' => '[data-test-author-name]',
            'checked_rating' => 'input[checked]',
            'comment' => '[data-test-comment]',
            'product_name' => '[data-test-product-name]',
            'rating' => '[data-test-rating="%value%"]',
            'title' => '[data-test-title]',
        ]);
    }
}
