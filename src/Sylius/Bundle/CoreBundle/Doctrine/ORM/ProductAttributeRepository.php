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

use Pagerfanta\PagerfantaInterface;
use Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository;

/**
 * Default product attribute repository.
 *
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class ProductAttributeRepository extends TranslatableResourceRepository
{
    /**
     * Create filter paginator.
     *
     * @param array   $criteria
     * @param array   $sorting
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator($criteria = array(), $sorting = array())
    {
        $queryBuilder = parent::getCollectionQueryBuilder();

        if (!empty($criteria)) {
            $this->applyCriteria($queryBuilder, $criteria);
        }

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = array();
            }
            $sorting['updatedAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAlias()
    {
        return 'product_attribute';
    }
}
