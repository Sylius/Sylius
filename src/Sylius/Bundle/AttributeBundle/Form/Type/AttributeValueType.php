<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\Type;

use Sylius\Bundle\AttributeBundle\Form\EventSubscriber\BuildAttributeValueFormSubscriber;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AttributeValueType extends AbstractResourceType
{
    /**
     * @var string
     */
    protected $subjectName;

    /**
     * @var EntityRepository
     */
    protected $attributeRepository;

    /**
     * @param string $dataClass
     * @param array $validationGroups
     * @param string $subjectName
     * @param EntityRepository $attributeRepository
     */
    public function __construct($dataClass, array $validationGroups, $subjectName, EntityRepository $attributeRepository)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->subjectName = $subjectName;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('attribute', sprintf('sylius_%s_attribute_choice', $this->subjectName), [
                'label' => sprintf('sylius.form.attribute.%s_attribute_value.attribute', $this->subjectName),
            ])
            ->addEventSubscriber(new BuildAttributeValueFormSubscriber($this->attributeRepository))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_attribute_value', $this->subjectName);
    }
}
