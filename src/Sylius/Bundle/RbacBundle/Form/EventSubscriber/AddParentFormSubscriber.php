<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Form\EventSubscriber;

use Sylius\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class AddParentFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     */
    public function __construct($type)
    {
        $this->type = $type;
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

        if (null === $resource) {
            return;
        }

        if (!$resource instanceof RoleInterface && !$resource instanceof PermissionInterface) {
            throw new UnexpectedTypeException($resource, '\RoleInterface or \PermissionInterface');
        }

        if (null !== $resource->getId() && null === $resource->getParent()) {
            return;
        }

        $form = $event->getForm();
        $form->add(
            'parent',
            sprintf('sylius_%s_choice', $this->type),
            ['label' => sprintf('sylius.form.%s.parent', $this->type)]
        );
    }
}
