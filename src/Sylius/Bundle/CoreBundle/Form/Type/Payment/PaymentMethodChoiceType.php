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

use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodChoiceType as BasePaymentMethodChoiceType;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
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
            $repositoryOptions = [
                'disabled' => $options['disabled'],
                'channel' => $options['channel'],
            ];

            return function (PaymentMethodRepositoryInterface $repository) use ($repositoryOptions) {
                return $repository->getQueryBuilderForChoiceType($repositoryOptions);
            };
        };

        $resolver
            ->setDefaults([
                'query_builder' => $queryBuilder,
                'channel' => null,
            ])
            ->setAllowedTypes('channel', [ChannelInterface::class, 'null'])
        ;
    }
}
