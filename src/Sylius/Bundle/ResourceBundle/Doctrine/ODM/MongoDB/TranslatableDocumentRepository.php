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
use Sylius\Bundle\ResourceBundle\Doctrine\TranslatableEntityRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

/**
 * Doctrine ORM driver translatable entity repository.
 *
 * @author Ivannis Suárez Jérez <ivannis.suarez@gmail.com>
 */
class TranslatableDocumentRepository extends DocumentRepository implements TranslatableEntityRepositoryInterface
{
    protected $localeContext;
    protected $translatableFields = array();

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $className = $this->getClassName();

        $object = new $className();
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
     * @param array $criteria
     */
    protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = null)
    {
        if (null === $criteria) {
            return;
        }

        foreach ($criteria as $property => $value) {
            if (in_array($property, $this->translatableFields)) {
                $property = 'translations.'.$this->getCurrentLocale().'.'.$property;
            }

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
     * @return string
     */
    protected function getCurrentLocale()
    {
        return $this->localeContext->getLocale();
    }
}
