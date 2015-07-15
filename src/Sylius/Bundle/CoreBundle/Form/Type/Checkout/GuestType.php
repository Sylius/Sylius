<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Checkout;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\UserBundle\Form\EventListener\CustomerRegistrationFormListener;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Checkout guest form type.
 *
 * @author Dmitrijs Balabka <dmitry.balabka@gmail.com>
 */
class GuestType extends AbstractResourceType
{
    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * @param string              $dataClass
     * @param array               $validationGroups
     * @param RepositoryInterface $customerRepository
     */
    public function __construct($dataClass, array $validationGroups = array(), RepositoryInterface $customerRepository)
    {
        parent::__construct($dataClass, $validationGroups);
        $this->customerRepository = $customerRepository;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'label' => 'sylius.form.guest.email',
            ))
            ->addEventSubscriber(new CustomerRegistrationFormListener($this->customerRepository))
            ->setDataLocked(false)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_checkout_guest';
    }
}
