<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Sylius\Bundle\ProductBundle\Doctrine\ORM\VariantRepository as BaseVariantRepository;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class ProductVariantRepository extends BaseVariantRepository implements ProductVariantRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFormQueryBuilder()
    {
        return $this->createQueryBuilder('o');
    }
}
