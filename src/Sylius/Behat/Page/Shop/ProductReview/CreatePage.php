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
    /**
     * {@inheritdoc}
     */
    public function getRouteName(): string
    {
        return 'sylius_shop_product_review_create';
    }

    /**
     * {@inheritdoc}
     */
    public function titleReview($title)
    {
        $this->getElement('title')->setValue($title);
    }

    /**
     * {@inheritdoc}
     */
    public function setComment($comment)
    {
        $this->getElement('comment')->setValue($comment);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthor($author)
    {
        $this->getElement('author')->setValue($author);
    }

    /**
     * {@inheritdoc}
     */
    public function rateReview($rate)
    {
        $this->getElement('rate')->selectOption($rate);
    }

    public function submitReview()
    {
        $this->getDocument()->pressButton('Add');
    }

    /**
     * {@inheritdoc}
     */
    public function getRateValidationMessage()
    {
        return $this->getElement('rating')->getParent()->find('css', '.sylius-validation-error')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getTitleValidationMessage()
    {
        return $this->getElement('title')->getParent()->find('css', '.sylius-validation-error')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getCommentValidationMessage()
    {
        return $this->getElement('comment')->getParent()->find('css', '.sylius-validation-error')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorValidationMessage()
    {
        return $this->getElement('author')->getParent()->find('css', '.sylius-validation-error')->getText();
    }

    /**
     * {@inheritdoc}
     */
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
