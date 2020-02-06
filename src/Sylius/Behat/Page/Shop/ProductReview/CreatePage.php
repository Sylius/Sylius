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

class CreatePage extends SymfonyPage implements CreatePageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_product_review_create';
    }

    public function titleReview(string $title): void
    {
        $this->getElement('title')->setValue($title);
    }

    public function setComment(string $comment): void
    {
        $this->getElement('comment')->setValue($comment);
    }

    public function setAuthor(string $author): void
    {
        $this->getElement('author')->setValue($author);
    }

    public function rateReview(int $rate): void
    {
        $this->getElement('rate')->selectOption($rate);
    }

    public function submitReview(): void
    {
        $this->getDocument()->pressButton('Add');
    }

    public function getRateValidationMessage(): string
    {
        return $this->getElement('rating')->getParent()->find('css', '.sylius-validation-error')->getText();
    }

    public function getTitleValidationMessage(): string
    {
        return $this->getElement('title')->getParent()->find('css', '.sylius-validation-error')->getText();
    }

    public function getCommentValidationMessage(): string
    {
        return $this->getElement('comment')->getParent()->find('css', '.sylius-validation-error')->getText();
    }

    public function getAuthorValidationMessage(): string
    {
        return $this->getElement('author')->getParent()->find('css', '.sylius-validation-error')->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'author' => '#sylius_product_review_author_email',
            'comment' => '#sylius_product_review_comment',
            'rate' => '[name="sylius_product_review[rating]"]',
            'rating' => '#sylius_product_review_rating',
            'title' => '#sylius_product_review_title',
        ]);
    }
}
