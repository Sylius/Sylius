<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\EventSubscriber;

use Sylius\Bundle\CoreBundle\Form\Type\User\ShopUserType;
use Sylius\Component\User\Model\UserAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Valid;
use Webmozart\Assert\Assert;

final class AddUserFormSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::SUBMIT => 'submit',
        ];
    }

    public function submit(FormEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();

        /** @var UserAwareInterface $data */
        Assert::isInstanceOf($data, UserAwareInterface::class);

        if (null === $data->getUser()->getPlainPassword() && null === $data->getUser()->getId()) {
            $data->setUser(null);
            $event->setData($data);

            $form->remove('user');
            $form->add('user', ShopUserType::class, ['constraints' => [new Valid()]]);
        }
    }
}
