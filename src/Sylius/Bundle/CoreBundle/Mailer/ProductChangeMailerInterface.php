<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Mailer;

use Sylius\Component\Core\Model\ProductVariantInterface;

interface ProductChangeMailerInterface
{
    /**
     * @param ProductVariantInterface $product
     * @param int                     $oldPrice
     * @param int                     $newPrice
     * @param string                  $email
     */
    public function sendPriceChange(ProductVariantInterface $product, $oldPrice, $newPrice, $email);

    /**
     * @param ProductVariantInterface $product
     * @param string                  $email
     *
     * @return mixed
     */
    public function sendStockChange(ProductVariantInterface $product, $email);
}
