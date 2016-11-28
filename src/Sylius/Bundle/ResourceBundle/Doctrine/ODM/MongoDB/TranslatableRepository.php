<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Doctrine\ODM\MongoDB;

use Doctrine\MongoDB\Query\Builder as QueryBuilder;
use Sylius\Component\Resource\Provider\LocaleProviderInterface;
use Sylius\Component\Resource\Repository\TranslatableRepositoryInterface;

/**
 * Doctrine ORM driver translatable entity repository.
 *
 * @author Ivannis Suárez Jérez <ivannis.suarez@gmail.com>
 */
class TranslatableRepository extends DocumentRepository implements TranslatableRepositoryInterface
{
    /**
     * @var LocaleProviderInterface
     */
    protected $localeProvider;

    /**
     * @var array
     */
    protected $translatableFields = [];

    /**
     * {@inheritdoc}
     */
    public function setLocaleProvider(LocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslatableFields(array $translatableFields)
    {
        $this->translatableFields = $translatableFields;

        return $this;
    }

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
                    ->field($this->getPropertyName($property))->in($value)
                ;
            } elseif ('' !== $value) {
                $queryBuilder
                    ->field($this->getPropertyName($property))->equals($value)
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
            $queryBuilder->sort($this->getPropertyName($property), $order);
        }
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getPropertyName($name)
    {
        if (in_array($name, $this->translatableFields, true)) {
            return 'translations.'.$this->localeProvider->getDefaultLocaleCode().'.'.$name;
        }

        return $name;
    }
}
