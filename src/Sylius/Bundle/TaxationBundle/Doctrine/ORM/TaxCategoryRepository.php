<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\TaxationBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Taxation\Repository\TaxCategoryRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @template T of TaxCategoryInterface
 *
 * @implements TaxCategoryRepositoryInterface<T>
 */
class TaxCategoryRepository extends EntityRepository implements TaxCategoryRepositoryInterface
{
    public function findByName(string $name): array
    {
        $taxCategory = $this->findBy(['name' => $name]);
        Assert::allIsInstanceOf($taxCategory, TaxCategoryInterface::class);

        return $taxCategory;
    }
}
