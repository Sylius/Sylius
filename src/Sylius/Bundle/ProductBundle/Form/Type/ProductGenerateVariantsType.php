<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ProductBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductGenerateVariantsType extends AbstractResourceType
{
    /**
     * @var EventSubscriberInterface
     */
    private $generateProductVariantsSubscriber;

    /**
     * @param string $dataClass
     * @param array|string[] $validationGroups
     * @param EventSubscriberInterface $generateProductVariants
     */
    public function __construct(string $dataClass, array $validationGroups, EventSubscriberInterface $generateProductVariants)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->generateProductVariantsSubscriber = $generateProductVariants;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('variants', CollectionType::class, [
                'entry_type' => ProductVariantGenerationType::class,
                'allow_add' => false,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->addEventSubscriber($this->generateProductVariantsSubscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_product_generate_variants';
    }
}
