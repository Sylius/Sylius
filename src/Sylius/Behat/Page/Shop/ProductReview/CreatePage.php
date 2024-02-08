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
use Webmozart\Assert\Assert;

class CreatePage extends SymfonyPage implements CreatePageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_product_review_create';
    }

    public function titleReview(?string $title): void
    {
        $this->getElement('title')->setValue($title);
    }

    public function setComment(?string $comment): void
    {
        $this->getElement('comment')->setValue($comment);
    }

    public function setAuthor(string $author): void
    {
        $this->getElement('author')->setValue($author);
    }

    public function rateReview(int $rate): void
    {
        $this->getElement('rating_option')->selectOption($rate);
    }

    public function submitReview(): void
    {
        $this->getElement('add')->press();
    }

    public function getRateValidationMessage(): string
    {
        return $this->getValidationMessageFor('rating');
    }

    public function getTitleValidationMessage(): string
    {
        return $this->getValidationMessageFor('title');
    }

    public function getCommentValidationMessage(): string
    {
        return $this->getValidationMessageFor('comment');
    }

    public function getAuthorValidationMessage(): string
    {
        return $this->getValidationMessageFor('author');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add' => '[data-test-add]',
            'author' => '[data-test-author-email]',
            'comment' => '[data-test-comment]',
            'rating' => '[data-test-rating]',
            'rating_error' => '[data-test-rating] [data-test-validation-error]',
            'rating_option' => '[data-test-rating] [data-test-option]',
            'title' => '[data-test-title]',
        ]);
    }

    private function getValidationMessageFor(string $element): string
    {
        $errorElement = $this->getElement($element)->getParent()->find('css', '[data-test-validation-error]');
        Assert::notNull($errorElement);

        return $errorElement->getText();
    }
}
