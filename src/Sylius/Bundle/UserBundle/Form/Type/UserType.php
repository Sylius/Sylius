<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\UserBundle\Form\EventListener\CanonicalizerFormListener;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserType extends AbstractResourceType
{
    /**
     * @var CanonicalizerInterface
     */
    protected $canonicalizer;

    /**
    * @param string                 $dataClass
    * @param string[]               $validationGroups
    * @param CanonicalizerInterface $canonicalizer
    */
    public function __construct($dataClass, array $validationGroups, CanonicalizerInterface $canonicalizer)
    {
        parent::__construct($dataClass, $validationGroups);
        $this->canonicalizer = $canonicalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new CanonicalizerFormListener($this->canonicalizer))
            ->add('customer', 'sylius_customer')
            ->add('plainPassword', 'password', array(
                'label' => 'sylius.form.user.password.label',
            ))
            ->add('enabled', 'checkbox', array(
                'label' => 'sylius.form.user.enabled',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_user';
    }
}
