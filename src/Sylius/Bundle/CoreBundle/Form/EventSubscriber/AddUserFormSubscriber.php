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

use Sylius\Component\User\Model\UserAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Valid;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
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
            FormEvents::SUBMIT => 'submit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $form->add('user', $this->entryType, ['constraints' => [new Valid()]]);
        $form->add('createUser', CheckboxType::class, [
            'label' => 'sylius.ui.customer_can_login_to_the_store',
            'required' => false,
            'mapped' => false,
        ]);
    }

    /**
     * @param FormEvent $event
     */
    public function submit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $form->get('createUser')->getViewData()) {
            Assert::isInstanceOf($data, UserAwareInterface::class);

            $data->setUser(null);
            $event->setData($data);

            $form->remove('user');
            $form->add('user', $this->entryType, ['constraints' => [new Valid()]]);
        }
    }
}
