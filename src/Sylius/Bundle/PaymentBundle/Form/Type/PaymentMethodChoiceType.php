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

use Doctrine\ORM\EntityRepository;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;
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
     * @var ServiceRegistryInterface
     */
    private $feeCalculatorRegistry;

    /**
     * @var ResourceRepositoryInterface
     */
    private $paymentRepository;

    /**
     * @param string                   $className
     * @param string                   $driver
     * @param string                   $name
     * @param ServiceRegistryInterface $feeCalculatorRegistry
     * @param ResourceRepositoryInterface      $paymentRepository
     */
    public function __construct($className, $driver, $name, ServiceRegistryInterface $feeCalculatorRegistry, ResourceRepositoryInterface $paymentRepository)
    {
        parent::__construct($className, $driver, $name);

        $this->feeCalculatorRegistry = $feeCalculatorRegistry;
        $this->paymentRepository = $paymentRepository;
    }

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

            return function (EntityRepository $repository) use ($repositoryOptions) {
                $queryBuilder = $repository->createQueryBuilder('o');

                if (!$repositoryOptions['disabled']) {
                    $queryBuilder->where('o.enabled = true');
                }

                return $queryBuilder;
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
