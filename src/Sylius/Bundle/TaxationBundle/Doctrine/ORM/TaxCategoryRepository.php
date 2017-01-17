<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Taxation\Repository\TaxCategoryRepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class TaxCategoryRepository extends EntityRepository implements TaxCategoryRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByName($name)
    {
        return $this->findBy(['name' => $name]);
    }
}
