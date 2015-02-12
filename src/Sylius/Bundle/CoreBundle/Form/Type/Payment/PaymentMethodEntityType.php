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
use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Payment method choice type
 *
 * @author Kristian Loevstroem <kristian@loevstroem.dk>
 */
class PaymentMethodEntityType extends PaymentMethodChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $queryBuilder = function (Options $options) {
            $channel = $options['channel'];
            $disabled = $options['disabled'];

            return function(EntityRepository $repository) use ($channel, $disabled) {
                $queryBuilder = $repository->createQueryBuilder('method');

                if (!$disabled) {
                    $queryBuilder->andWhere('method.enabled = true');
                }

                if ($channel) {
                    $queryBuilder->andWhere('method IN (:methods)')->setParameter('methods', $channel->getPaymentMethods()->toArray());
                }

                return $queryBuilder;
            };
        };

        $resolver
            ->setDefaults(array(
                'query_builder' => $queryBuilder,
                'channel'       => null
            ))
            ->setAllowedTypes(array(
                'channel'  => array('Sylius\Component\Channel\Model\ChannelInterface', 'null'),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }
}
