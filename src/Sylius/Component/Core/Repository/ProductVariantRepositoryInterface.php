<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Repository;

use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * This interface should be implemented by repository of product variants.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ProductVariantRepositoryInterface extends ResourceRepositoryInterface
{
    /**
     * Get query builder for the form choice field.
     */
    public function getFormQueryBuilder();
}
