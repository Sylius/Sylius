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
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Calculator\FeeCalculatorInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
     * @var ServiceRegistryInterface
     */
    private $feeCalculatorRegistry;

    /**
     * @var RepositoryInterface
     */
    private $paymentRepository;

    /**
     * @param string                   $className
     * @param string                   $driver
     * @param string                   $name
     * @param ServiceRegistryInterface $feeCalculatorRegistry
     * @param RepositoryInterface      $paymentRepository
     */
    public function __construct($className, $driver, $name, ServiceRegistryInterface $feeCalculatorRegistry, RepositoryInterface $paymentRepository)
    {
        parent::__construct($className, $driver, $name);

        $this->feeCalculatorRegistry = $feeCalculatorRegistry;
        $this->paymentRepository = $paymentRepository;
    }

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
        };;

        $resolver
            ->setDefaults(array(
                'query_builder' => $queryBuilder,
                'disabled'      => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!$parent = $view->parent->vars['value'] instanceof Order) {
            return;
        }

        $paymentCosts = array();

        $payment = $view->parent->vars['value']->getPayments()->last();

        foreach ($view->vars['choices'] as $choiceView) {
            $method = $choiceView->data;

            if (!$method instanceof PaymentMethodInterface) {
                throw new UnexpectedTypeException($method, 'Sylius\Component\Payment\Model\PaymentMethodInterface');
            }

            $feeCalculator = $this->feeCalculatorRegistry->get($method->getFeeCalculator());
            $payment->setMethod($method);

            $paymentCosts[$choiceView->value] = $feeCalculator->calculate($payment, $method->getFeeCalculatorConfiguration());
        }

        $view->vars['paymentCosts'] = $paymentCosts;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sylius_payment_method_choice';
    }
}
