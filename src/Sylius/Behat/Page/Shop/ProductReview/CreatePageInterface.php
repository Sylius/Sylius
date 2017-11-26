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

use Sylius\Behat\Page\PageInterface;

interface CreatePageInterface extends PageInterface
{
    /**
     * @param string $title
     */
    public function titleReview(string $title): void;

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void;

    /**
     * @param string $author
     */
    public function setAuthor(string $author): void;

    /**
     * @param int $rate
     */
    public function rateReview(int $rate): void;

    public function submitReview(): void;

    /**
     * @return string
     */
    public function getRateValidationMessage(): string;

    /**
     * @return string
     */
    public function getTitleValidationMessage(): string;

    /**
     * @return string
     */
    public function getCommentValidationMessage(): string;

    /**
     * @return string
     */
    public function getAuthorValidationMessage(): string;
}
