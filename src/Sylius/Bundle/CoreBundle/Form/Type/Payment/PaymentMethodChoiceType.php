<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Payment;

use Doctrine\ORM\EntityRepository;
use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodChoiceType as BasePaymentMethodChoiceType;
<<<<<<< HEAD
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;
=======
>>>>>>> Fix specs
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Payment method choice type
 *
 * @author Kristian Loevstroem <kristian@loevstroem.dk>
 */
class PaymentMethodChoiceType extends BasePaymentMethodChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $queryBuilder = function (Options $options) {
            $repositoryOptions = array(
                'disabled' => $options['disabled'],
                'channel' => $options['channel'],
            );

            return function (EntityRepository $repository) use ($repositoryOptions) {
                $queryBuilder = $repository->createQueryBuilder('o');

                if (!$repositoryOptions['disabled']) {
                    $queryBuilder->where('o.enabled = true');
                }
                if ($repositoryOptions['channel']) {
                    $queryBuilder
                        ->andWhere('o IN (:methods)')
                        ->setParameter('methods', $repositoryOptions['channel']->getPaymentMethods()->toArray())
                    ;
                }

                return $queryBuilder;
            };
        };

        $resolver
            ->setDefaults(array(
                'query_builder' => $queryBuilder,
                'channel' => null
            ))
            ->setAllowedTypes('channel', [ChannelInterface::class, 'null'])
        ;
    }
}
