<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminApiBundle\Form\EventSubscriber;

use Sylius\Component\User\Model\UserAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Valid;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class AddUserFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $entryType;

    /**
     * @param string $entryType
     */
    public function __construct($entryType)
    {
        $this->entryType = $entryType;
    }

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
        $form->add('user', $this->entryType, ['constraints' => [new Valid()]]);
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
        Assert::isInstanceOf($normData, UserAwareInterface::class);
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
