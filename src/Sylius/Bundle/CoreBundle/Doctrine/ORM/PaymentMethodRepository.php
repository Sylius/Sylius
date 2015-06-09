<?php

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Sylius\Bundle\PaymentBundle\Doctrine\ORM\PaymentMethodRepository as BasePaymentMethodRepository;

class PaymentMethodRepository extends BasePaymentMethodRepository
{
    /**
     * {@inheritdoc}
     */
    public function getQueryBuidlerForChoiceType(array $options)
    {
        $queryBuilder = parent::getQueryBuidlerForChoiceType($options);

        if ($options['channel']) {
            $queryBuilder->andWhere('method IN (:methods)')
                ->setParameter('methods', $options['channel']->getPaymentMethods()->toArray());
        }

        return $queryBuilder;
    }
}
