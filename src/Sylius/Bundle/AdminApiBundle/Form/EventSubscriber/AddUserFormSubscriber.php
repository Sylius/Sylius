<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminApiBundle\Form\EventSubscriber;

use Sylius\Component\User\Model\UserAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Valid;
use Webmozart\Assert\Assert;

final class AddUserFormSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private $entryType;

    public function __construct(string $entryType)
    {
        $this->entryType = $entryType;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        $form->add('user', $this->entryType, ['constraints' => [new Valid()]]);
    }

    public function preSubmit(FormEvent $event): void
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

    private function isUserDataEmpty(array $data): bool
    {
        foreach ($data['user'] as $field) {
            if (!empty($field)) {
                return false;
            }
        }

        return true;
    }

    private function removeUserField(FormEvent $event): void
    {
        $form = $event->getForm();
        $form->remove('user');
    }
}
