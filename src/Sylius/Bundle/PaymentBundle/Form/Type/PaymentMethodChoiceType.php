<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Payment method choice type for document/entity/phpcr_document choice form types.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class PaymentMethodChoiceType extends ResourceChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $queryBuilder = function (Options $options) {
            $repositoryOptions = array(
                'disabled' => $options['disabled'],
            );

            return function (PaymentMethodRepositoryInterface $repository) use ($repositoryOptions) {
                return $repository->getQueryBuidlerForChoiceType($repositoryOptions);
            };
        };

        $resolver
            ->setDefaults(array(
                'query_builder' => $queryBuilder,
                'disabled' => false,
            ))
        ;
    }

}
