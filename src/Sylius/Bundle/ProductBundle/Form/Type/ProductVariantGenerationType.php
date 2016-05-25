<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ProductVariantGenerationType extends AbstractResourceType
{
    /**
     * @var EventSubscriberInterface
     */
    private $generateProductVariants;

    /**
     * @param string $dataClass FQCN
     * @param string[] $validationGroups
     * @param EventSubscriberInterface $generateProductVariants
     */
    public function __construct($dataClass, $validationGroups, EventSubscriberInterface $generateProductVariants)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->generateProductVariants = $generateProductVariants;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('variants', 'collection', [
                'type' => 'sylius_product_variant',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->addEventSubscriber($this->generateProductVariants);
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product_variant_generation';
    }
}
