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

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CreatePage extends SymfonyPage implements CreatePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_product_reviews_create';
    }

    /**
     * {@inheritdoc}
     */
    public function titleReview($title)
    {
        // TODO: Implement titleReview() method.
    }

    /**
     * {@inheritdoc}
     */
    public function setComment($comment)
    {
        // TODO: Implement setComment() method.
    }

    /**
     * {@inheritdoc}
     */
    public function rateReview($rate)
    {
        // TODO: Implement rateReview() method.
    }

    public function submitReview()
    {
        // TODO: Implement submitReview() method.
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), []);
    }
}
