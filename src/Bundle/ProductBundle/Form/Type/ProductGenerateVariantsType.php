<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
     * @param array|string[] $validationGroups
     */
    public function __construct(
        string $dataClass,
        array $validationGroups,
        private EventSubscriberInterface $generateProductVariantsSubscriber,
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('variants', CollectionType::class, [
                'entry_type' => ProductVariantGenerationType::class,
                'allow_add' => false,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->addEventSubscriber($this->generateProductVariantsSubscriber)
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_product_generate_variants';
    }
}
