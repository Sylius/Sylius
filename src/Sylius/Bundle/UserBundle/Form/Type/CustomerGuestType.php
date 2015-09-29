<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 * @author Dmitrijs Balabka <dmitry.balabka@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CustomerGuestType extends AbstractResourceType
{
    /**
     * @var EventSubscriberInterface
     */
    private $eventSubscriber;

    /**
     * @param string                   $dataClass
     * @param array                    $validationGroups
     * @param EventSubscriberInterface $eventSubscriber
     */
    public function __construct($dataClass, array $validationGroups, EventSubscriberInterface $eventSubscriber)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->eventSubscriber = $eventSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add('email', 'email', array(
                'label' => 'sylius.form.customer.email',
            ))
            ->addEventSubscriber($this->eventSubscriber)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_customer_guest';
    }
}
