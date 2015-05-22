<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Form\Type;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Calculator\Registry\CalculatorRegistryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Resolver\MethodsResolverInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * A select form which allows the user to select
 * a method that supports given shippables aware.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ShippingMethodChoiceType extends AbstractType
{
    /**
     * Supported methods resolver.
     *
     * @var MethodsResolverInterface
     */
    protected $resolver;

    /**
     * @var CalculatorRegistryInterface
     */
    protected $calculators;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param MethodsResolverInterface    $resolver
     * @param CalculatorRegistryInterface $calculators
     * @param Repositoryinterface         $repository
     */
    public function __construct(MethodsResolverInterface $resolver, CalculatorRegistryInterface $calculators, RepositoryInterface $repository)
    {
        $this->resolver = $resolver;
        $this->calculators = $calculators;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder->addModelTransformer(new CollectionToArrayTransformer());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $methodsResolver = $this->resolver;
        $repository = $this->repository;

        $choiceList = function (Options $options) use ($methodsResolver, $repository) {
            if (isset($options['subject'])) {
                $methods = $methodsResolver->getSupportedMethods($options['subject'], $options['criteria']);
            } else {
                $methods = $repository->findBy($options['criteria']);
            }

            return new ObjectChoiceList($methods);
        };

        $resolver
            ->setDefaults(array(
                'choice_list' => $choiceList,
                'criteria'    => array()
            ))
            ->setOptional(array(
                'subject',
            ))
            ->setAllowedTypes(array(
                'subject'  => array('Sylius\Component\Shipping\Model\ShippingSubjectInterface'),
                'criteria' => array('array')
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!isset($options['subject'])) {
            return;
        }

        $subject = $options['subject'];
        $shippingCosts = array();

        foreach ($view->vars['choices'] as $choiceView) {
            $method = $choiceView->data;

            if (!$method instanceof ShippingMethodInterface) {
                throw new UnexpectedTypeException($method, 'ShippingMethodInterface');
            }

            $calculator = $this->calculators->getCalculator($method->getCalculator());
            $shippingCosts[$choiceView->value] = $calculator->calculate($subject, $method->getConfiguration());
        }

        $view->vars['shipping_costs'] = $shippingCosts;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_shipping_method_choice';
    }
}
