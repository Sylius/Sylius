<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class AddUserFormSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $form->add('user', 'sylius_user');
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $normData = $event->getForm()->getNormData();

        if (!isset($data['user'])) {
            $this->removeUserField($event);

            return;
        }

        if ($this->isUserDataEmpty($data) && null === $normData->getUser()) {
            unset($data['user']);
            $event->setData($data);
            $this->removeUserField($event);
        }
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    private function isUserDataEmpty(array $data)
    {
        foreach ($data['user'] as $field) {
            if (!empty($field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param FormEvent $event
     */
    private function removeUserField(FormEvent $event)
    {
        $form = $event->getForm();
        $form->remove('user');
    }
}
