<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AssociationBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class AssociationTypeType extends AbstractResourceType
{
    /**
     * @var string
     */
    private $subject;

    /**
     * @param string   $dataClass
     * @param string[] $validationGroups
     * @param string   $subject
     */
    public function __construct($dataClass, array $validationGroups = array(), $subject)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->subject = $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.product_association_type.name',
                'required' => true
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_association_type', $this->subject);
    }
}
