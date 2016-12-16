<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Component\Customer\Context\CustomerContextInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ContactType extends AbstractType
{
    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @param CustomerContextInterface $customerContext
     */
    public function __construct(CustomerContextInterface $customerContext)
    {
        $this->customerContext = $customerContext;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'sylius.ui.email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.contact.email.not_blank',
                    ]),
                    new Email([
                        'message' => 'sylius.contact.email.invalid',
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'sylius.ui.message',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.contact.message.not_blank',
                    ]),
                ],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $customer = $this->customerContext->getCustomer();

                if (null === $customer) {
                    return;
                }

                $data = $event->getData();
                $data['email'] = $customer->getEmail();

                $event->setData($data);
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_contact';
    }
}
