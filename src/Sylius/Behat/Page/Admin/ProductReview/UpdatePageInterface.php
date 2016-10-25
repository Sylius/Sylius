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

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @param string $title
     */
    public function specifyTitle($title);

    /**
     * @param string $comment
     */
    public function specifyComment($comment);

    /**
     * @param string $rating
     */
    public function chooseRating($rating);

    /**
     * @return string
     */
    public function getRating();

    /**
     * @return string
     */
    public function getProductName();

    /**
     * @return string
     */
    public function getCustomerName();
}
