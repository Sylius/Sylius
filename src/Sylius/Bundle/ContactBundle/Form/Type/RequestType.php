<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContactBundle\Form\Type;

use Sylius\Bundle\CustomerBundle\Form\Type\CustomerType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Customer\Model\CustomerAwareInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Sylius contact request form type.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class RequestType extends AbstractResourceType
{
    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    public function __construct($dataClass, array $validationGroups = array(), SecurityContextInterface $securityContext)
    {
        parent::__construct($dataClass, $validationGroups);

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
        $data->setCustomer($customer);

        $builder
            ->add('customer', 'sylius_customer')
            ->add('message', 'textarea', array(
                'label' => 'sylius.form.contact_request.message',
            ))
            ->add('topic', 'sylius_contact_topic_choice', array(
                'label'    => 'sylius.form.contact_request.topic',
                'required' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'cascade_validation' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_contact_request';
    }
}
