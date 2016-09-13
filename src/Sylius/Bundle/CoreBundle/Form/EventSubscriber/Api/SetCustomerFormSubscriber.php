<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\EventSubscriber\Api;

use Sylius\Component\Customer\Model\CustomerAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class SetCustomerFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $field;

    /**
     * @param string $field
     */
    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $resource = $event->getData();
        $customer = $event->getForm()->getConfig()->getOption($this->field);

        Assert::isInstanceOf($resource, CustomerAwareInterface::class);

        $resource->setCustomer($customer);

        $event->setData($resource);
    }
}
