<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TranslationBundle\Doctrine\ODM\MongoDB;

use Doctrine\MongoDB\Query\Builder as QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ODM\MongoDB\DocumentRepository;
use Sylius\Component\Translation\Provider\LocaleProviderInterface;
use Sylius\Component\Translation\Repository\TranslatableResourceRepositoryInterface;

/**
 * Doctrine ORM driver translatable entity repository.
 *
 * @author Ivannis Suárez Jérez <ivannis.suarez@gmail.com>
 */
class TranslatableResourceRepository extends DocumentRepository implements TranslatableResourceRepositoryInterface
{
    /**
     * @var LocaleProviderInterface
     */
    protected $localeProvider;

    /**
     * @var array
     */
    protected $translatableFields = array();

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $className = $this->getClassName();

        $object = new $className();

        $object->setCurrentLocale($this->localeProvider->getCurrentLocale());
        $object->setFallbackLocale($this->localeProvider->getFallbackLocale());

        return $object;
    }

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
     * {inheritdoc}.
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
     * {inheritdoc}.
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
        if (in_array($name, $this->translatableFields)) {
            return 'translations.'.$this->localeProvider->getCurrentLocale().'.'.$name;
        }

        return $name;
    }
}
