<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Checkout;

use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddPaymentMethodsFormSubscriber;
<<<<<<< f574da9415fca0469004bb57b870d9ce06a2cb6a
=======
use Sylius\Component\Core\Model\ChannelInterface;
>>>>>>> [ShopBundle] Implementation of checkout payment
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class PaymentType extends AbstractType
{
    /**
     * @var string
     */
<<<<<<< f574da9415fca0469004bb57b870d9ce06a2cb6a
    private $dataClass;
=======
    protected $dataClass;
>>>>>>> [ShopBundle] Implementation of checkout payment

    /**
     * @param string $dataClass
     */
    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new AddPaymentMethodsFormSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
<<<<<<< f574da9415fca0469004bb57b870d9ce06a2cb6a
        $resolver->setDefault('data_class', $this->dataClass);
    }

=======
        $resolver
            ->setDefaults([
                'data_class' => $this->dataClass,
            ])
            ->setDefined([
                'channel',
            ])
            ->setAllowedTypes('channel', [ChannelInterface::class, 'null'])
        ;
    }
    
>>>>>>> [ShopBundle] Implementation of checkout payment
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_checkout_payment';
    }
}
