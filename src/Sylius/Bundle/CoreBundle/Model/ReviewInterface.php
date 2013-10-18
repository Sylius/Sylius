<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Model;

use Sylius\Bundle\ReviewBundle\Model\ReviewInterface as BaseReviewInterface;

/**
 * ReviewInterface
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface ReviewInterface extends BaseReviewInterface
{
    /**
     * Get Product.
     *
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * Set Product.
     *
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product);

    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);
}
