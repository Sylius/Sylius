<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxation\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface TaxCategoryRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $name
     *
     * @return TaxCategoryInterface[]
     */
    public function findByName($name);
}
