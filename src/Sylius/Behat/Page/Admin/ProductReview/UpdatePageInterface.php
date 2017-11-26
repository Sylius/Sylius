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

namespace Sylius\Behat\Page\Admin\ProductReview;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @param string $title
     */
    public function specifyTitle(string $title): void;

    /**
     * @param string $comment
     */
    public function specifyComment(string $comment): void;

    /**
     * @param string $rating
     */
    public function chooseRating(string $rating): void;

    /**
     * @return string
     */
    public function getRating(): string;

    /**
     * @return string
     */
    public function getProductName(): string;

    /**
     * @return string
     */
    public function getCustomerName(): string;
}
