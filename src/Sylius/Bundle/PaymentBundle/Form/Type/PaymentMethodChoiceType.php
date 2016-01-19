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
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Payment method choice type for document/entity/phpcr_document choice form types.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PaymentMethodChoiceType extends ResourceChoiceType
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
            );

            return function (PaymentMethodRepositoryInterface $repository) use ($repositoryOptions) {
                return $repository->getQueryBuidlerForChoiceType($repositoryOptions);
            };
        };;

        $resolver
            ->setDefaults(array(
                'query_builder' => $queryBuilder,
                'disabled'      => false,
            ))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sylius_payment_method_choice';
    }
}
