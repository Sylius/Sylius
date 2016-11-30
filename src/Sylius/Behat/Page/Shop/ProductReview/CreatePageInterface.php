<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\ProductReview;

use Sylius\Behat\Page\PageInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface CreatePageInterface extends PageInterface
{
    /**
     * @param string $title
     */
    public function titleReview($title);

    /**
     * @param string $comment
     */
    public function setComment($comment);

    /**
     * @param string $author
     */
    public function setAuthor($author);

    /**
     * @param int $rate
     */
    public function rateReview($rate);

    public function submitReview();

    /**
     * @return string
     */
    public function getRateValidationMessage();

    /**
     * @return string
     */
    public function getTitleValidationMessage();

    /**
     * @return string
     */
    public function getCommentValidationMessage();

    /**
     * @return string
     */
    public function getAuthorValidationMessage();
}
