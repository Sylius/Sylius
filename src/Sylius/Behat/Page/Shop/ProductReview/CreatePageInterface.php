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
    public function titleReview(string $title);

    public function setComment(string $comment);

    public function setAuthor(string $author);

    public function rateReview(int $rate);

    public function submitReview();

    public function getRateValidationMessage(): string;

    public function getTitleValidationMessage(): string;

    public function getCommentValidationMessage(): string;

    public function getAuthorValidationMessage(): string;
}
