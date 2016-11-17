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
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AttributeValueType extends AbstractResourceType
{
    /**
     * @var EntityRepository
     */
    protected $attributeRepository;

    /**
     * @param string $dataClass
     * @param array $validationGroups
     * @param EntityRepository $attributeRepository
     */
    public function __construct($dataClass, array $validationGroups, EntityRepository $attributeRepository)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->attributeRepository = $attributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('attribute', ResourceChoiceType::class, [
                'resource' => $options['resource'],
                'label' => 'sylius.form.attribute.attribute_value.attribute',
            ])
            ->addEventSubscriber(new BuildAttributeValueFormSubscriber($this->attributeRepository))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('resource');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_attribute_value';
    }
}
