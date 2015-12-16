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

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Intl\Exception\UnexpectedTypeException;

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
     * @param string $type
     */
    public function __construct($type = 'text')
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $resource = $event->getData();

        if (!$resource instanceof CodeAwareInterface) {
            throw new UnexpectedTypeException($resource, CodeAwareInterface::class);
        }

        $form = $event->getForm();
        $disabled = (null !== $resource->getCode());


        $form->add('code', $this->type, array('label' => 'sylius.ui.code', 'disabled' => $disabled));
    }
}
