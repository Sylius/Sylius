<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\TranslatableEntityRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

/**
 * Doctrine ORM driver translatable entity repository.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TranslatableEntityRepository extends EntityRepository implements TranslatableEntityRepositoryInterface
{
    protected $localeContext;
    protected $translatableFields = array();

    /**
     * {@inheritdoc}
     */
    protected function getQueryBuilder()
    {
        return parent::getQueryBuilder()
            ->leftJoin($this->getAlias() . '.translations', 'translation');
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $className = $this->getClassName();

        $object = new $className;
        $object->setCurrentLocale($this->getCurrentLocale());

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocaleContext(LocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;

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
     * @param QueryBuilder $queryBuilder
     *
     * @param array        $criteria
     */
    protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = null)
    {
        if (null === $criteria) {
            return;
        }

        foreach ($criteria as $property => $value) {

            if (in_array($property, $this->translatableFields)) {
                $property = 'translation.' . $property;
                if (null === $value) {
                    $queryBuilder
                        ->andWhere($queryBuilder->expr()->isNull($property));
                } elseif (is_array($value)) {
                    $queryBuilder->andWhere($queryBuilder->expr()->in($property, $value));
                } elseif ('' !== $value) {
                    $parameter = str_replace('.', '_', $property);
                    $queryBuilder
                        ->andWhere($queryBuilder->expr()->eq($property, ':' . $parameter))
                        ->setParameter($parameter, $value);
                }
            } else {
                if (null === $value) {
                    $queryBuilder
                        ->andWhere($queryBuilder->expr()->isNull($this->getPropertyName($property)));
                } elseif (is_array($value)) {
                    $queryBuilder->andWhere($queryBuilder->expr()->in($this->getPropertyName($property), $value));
                } elseif ('' !== $value) {
                    $queryBuilder
                        ->andWhere($queryBuilder->expr()->eq($this->getPropertyName($property), ':' . $property))
                        ->setParameter($property, $value);
                }
            }
        }
    }

    /**
     * @return string
     */
    protected function getCurrentLocale()
    {
        return $this->localeContext->getLocale();
    }
}
