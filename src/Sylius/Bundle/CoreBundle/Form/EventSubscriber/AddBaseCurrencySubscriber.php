<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class AddBaseCurrencySubscriber implements EventSubscriberInterface
{
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
        $disabled = $this->getDisabledOption($resource);

        $form = $event->getForm();
        $form->add('baseCurrency', CurrencyChoiceType::class, [
            'label' => 'sylius.form.channel.currency_base',
            'required' => true,
            'disabled' => $disabled,
        ]);
    }

    /**
     * @param mixed $resource
     *
     * @return bool
     *
     * @throws UnexpectedTypeException
     */
    private function getDisabledOption($resource)
    {
        if ($resource instanceof ChannelInterface) {
            return null !== $resource->getId();
        }

        if (null === $resource) {
            return false;
        }

        throw new UnexpectedTypeException($resource, ChannelInterface::class);
    }
}
