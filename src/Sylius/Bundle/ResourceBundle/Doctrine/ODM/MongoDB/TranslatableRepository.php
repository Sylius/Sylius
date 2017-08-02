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

namespace Sylius\Bundle\ResourceBundle\Doctrine\ODM\MongoDB;

use Doctrine\MongoDB\Query\Builder as QueryBuilder;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Sylius\Component\Resource\Repository\TranslatableRepositoryInterface;

/**
 * Doctrine ORM driver translatable entity repository.
 *
 * @author Ivannis Suárez Jérez <ivannis.suarez@gmail.com>
 */
class TranslatableRepository extends DocumentRepository implements TranslatableRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = null)
    {
        if (null === $criteria) {
            return;
        }

        foreach ($criteria as $property => $value) {
            if (is_array($value)) {
                $queryBuilder
                    ->field($property)->in($value)
                ;
            } elseif ('' !== $value) {
                $queryBuilder
                    ->field($property)->equals($value)
                ;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function applySorting(QueryBuilder $queryBuilder, array $sorting = null)
    {
        if (null === $sorting) {
            return;
        }

        foreach ($sorting as $property => $order) {
            $queryBuilder->sort($property, $order);
        }
    }
}
