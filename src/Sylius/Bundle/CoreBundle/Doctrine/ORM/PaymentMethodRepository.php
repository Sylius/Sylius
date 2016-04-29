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

use Sylius\Bundle\PaymentBundle\Doctrine\ORM\PaymentMethodRepository as BasePaymentMethodRepository;

class PaymentMethodRepository extends BasePaymentMethodRepository
{
    /**
     * {@inheritdoc}
     */
    public function createListQueryBuilder()
    {
        return $this
            ->createQueryBuilder('o')
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilderForChoiceType(array $options)
    {
        $queryBuilder = parent::getQueryBuilderForChoiceType($options);

        if ($options['channel']) {
            $queryBuilder->andWhere('o IN (:methods)')
                ->setParameter('methods', $options['channel']->getPaymentMethods()->toArray());
        }

        return $queryBuilder;
    }
}
