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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Validator\Constraints\Email;

/**
 * Checkout guest form type.
 *
 * @author Dmitrijs Balabka <dmitry.balabka@gmail.com>
 */
class GuestType extends AbstractResourceType
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
        $email = null;
        if ($this->securityContext->isGranted('IS_CUSTOMER')) {
            $email = $this->securityContext->getToken()->getUser()->getEmail();
        }

        $builder
            ->add('email', 'email', array(
                'empty_data'  => $email,
                'constraints' => array(
                    new Email(),
                ),
            ))
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
