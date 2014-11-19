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
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Doctrine\TranslatableEntityRepositoryInterface;
// We only need the get method from  LocaleContextInterface, should I define a new interface with just this method?
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
            ->addSelect($this->getAlias().', translation')
            ->leftJoin($this->getAlias().'.translations', 'translation')
            ;
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
     * @param mixed $contextLocale
     */
    public function setLocaleContext(LocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslatableFields(array $translatableFields)
    {
        $this->translatableFields = $translatableFields;
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @param array $criteria
     */
    protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = null)
    {
        if (null === $criteria) {
            return;
        }

        foreach ($criteria as $property => $value) {

            if (in_array($property, $this->translatableFields)) {
                $property = 'translation.'.$property;
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
            }else{
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
     * @return mixed
     */
    protected function getCurrentLocale()
    {
        return $this->localeContext->getLocale();
    }
}
