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

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class AssociationType extends AbstractResourceType
{
    /**
     * @var string
     */
    protected $subjectName;

    /**
     * @param string $dataClass
     * @param array $validationGroups
     * @param string $subjectName
     */
    public function __construct($dataClass, array $validationGroups, $subjectName)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->subjectName = $subjectName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', sprintf('sylius_%s_association_type_choice', $this->subjectName), [
                'label' => sprintf('sylius.form.%s_association.type', $this->subjectName),
            ])
            ->add($this->subjectName, sprintf('sylius_%s_choice', $this->subjectName), [
                'label' => sprintf('sylius.form.%s_association.%s', $this->subjectName, $this->subjectName),
                'property_path' => 'associatedObjects',
                'multiple' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_association', $this->subjectName);
    }
}
