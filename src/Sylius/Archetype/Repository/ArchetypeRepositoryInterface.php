<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Archetype\Repository;

use Sylius\Archetype\Model\ArchetypeInterface;
use Sylius\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ArchetypeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $name
     *
     * @return ArchetypeInterface|null
     */
    public function findOneByName($name);
}
