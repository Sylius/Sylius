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
interface IndexPageInterface extends PageInterface
{
    /**
     * @return int
     */
    public function countReviews();

    /**
     * @param string $title
     *
     * @return bool
     */
    public function hasReviewTitled($title);

    /**
     * @return bool
     */
    public function hasNoReviewsMessage();
}
