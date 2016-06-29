<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Taxation\Repository;

use Sylius\Resource\Repository\RepositoryInterface;
use Sylius\Taxation\Model\TaxCategoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface TaxCategoryRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $name
     *
     * @return TaxCategoryInterface|null
     */
    public function findOneByName($name);
}
