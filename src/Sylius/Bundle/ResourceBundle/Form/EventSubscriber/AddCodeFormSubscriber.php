<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\EventSubscriber;

use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class AddCodeFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $label;

    /**
     * @param string $type
     * @param string $label
     */
    public function __construct($type = 'text', $label = 'sylius.ui.code')
    {
        $this->type = $type;
        $this->label = $label;
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
        $disabled = false;

        if ($resource instanceof CodeAwareInterface) {
            $disabled = null !== $resource->getCode();
        } elseif (null !== $resource) {
            throw new UnexpectedTypeException($resource, CodeAwareInterface::class);
        }

        $form = $event->getForm();
        $form->add('code', $this->type, ['label' => $this->label, 'disabled' => $disabled]);
    }
}
