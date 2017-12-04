<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\TaxationBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Taxation\Repository\TaxCategoryRepositoryInterface;

class TaxCategoryRepository extends EntityRepository implements TaxCategoryRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByName(string $name): array
    {
        return $this->findBy(['name' => $name]);
    }
}
