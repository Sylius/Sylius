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

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Sylius\Component\Customer\Model\CustomerAwareInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class RegistrationFormType extends BaseType
{
    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    public function __construct($class, SecurityContextInterface $securityContext)
    {
        parent::__construct($class);

        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $customer = $this->securityContext->getToken()->getUser();
            if ($customer instanceof CustomerAwareInterface) {
                $customer = $customer->getCustomer();
            }
        } else {
            $customer = null;
        }

        $data = $builder->getData();
        if ($data instanceof CustomerAwareInterface) {
            $data->setCustomer($customer);
        }

        parent::buildForm($builder, $options);

        // remove the `username` & `email` fields
        $builder
            ->remove('username')
            ->remove('email')
            ->add('customer', 'sylius_customer')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_user_registration';
    }
}
