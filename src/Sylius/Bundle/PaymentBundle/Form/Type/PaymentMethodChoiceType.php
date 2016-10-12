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

use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class PaymentMethodChoiceType extends AbstractType
{
    /**
     * @var PaymentMethodsResolverInterface
     */
    protected $paymentMethodsResolver;

    /**
     * @var RepositoryInterface
     */
    protected $paymentMethodRepository;

    /**
     * @param PaymentMethodsResolverInterface $paymentMethodsResolver
     * @param RepositoryInterface $paymentMethodRepository
     */
    public function __construct(
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        RepositoryInterface $paymentMethodRepository
    ) {
        $this->paymentMethodsResolver = $paymentMethodsResolver;
        $this->paymentMethodRepository = $paymentMethodRepository;
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $choiceList = $this->createChoiceList();

        $resolver
            ->setDefaults([
                'choice_list' => $choiceList,
            ])
            ->setDefined([
                'subject',
            ])
            ->setAllowedTypes('subject', PaymentInterface::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sylius_payment_method_choice';
    }

    /**
     * @return \Closure
     */
    private function createChoiceList()
    {
        return function (Options $options) 
        {
            if (isset($options['subject'])) {
                $resolvedMethods = $this->paymentMethodsResolver->getSupportedMethods($options['subject']);
            } else {
                $resolvedMethods = $this->paymentMethodRepository->findAll();
            }

            return new ObjectChoiceList($resolvedMethods, null, [], null, 'id');
        };
    }
}
