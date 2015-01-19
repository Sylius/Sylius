<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxonomy\Repository;

use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * Taxonomy repository interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface TaxonomyRepositoryInterface extends ResourceRepositoryInterface
{
    /**
     * Find taxonomy by name.
     *
     * @param string $name
     *
     * @return null|TaxonomyInterface
     */
    public function findOneByName($name);
}
