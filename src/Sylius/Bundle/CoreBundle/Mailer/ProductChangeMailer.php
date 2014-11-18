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

class ProductChangeMailer extends AbstractMailer implements ProductChangeMailerInterface
{
    /**
     * {@inheritdoc}
     */
    public function sendPriceChange(ProductVariantInterface $product, $oldPrice, $newPrice, $email)
    {
        $this->sendEmail(array('product' => $product, 'old_price' => $oldPrice, 'new_price' => $newPrice), $email);
    }

    /**
     * {@inheritdoc}
     */
    public function sendStockChange(ProductVariantInterface $product, $email)
    {
        $this->sendEmail(array('product' => $product), $email);
    }
}
