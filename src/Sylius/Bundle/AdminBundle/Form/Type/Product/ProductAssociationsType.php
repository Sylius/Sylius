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

namespace Sylius\Bundle\AdminBundle\Form\Type\Product;

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ResourceBundle\Form\Type\FixedCollectionType;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductAssociationsType extends AbstractType
{
    /**
     * @param RepositoryInterface<ProductAssociationTypeInterface> $productAssociationTypeRepository
     * @param DataTransformerInterface<Collection<array-key, ProductAssociationInterface>, Collection<array-key, ProductInterface>> $productsToProductAssociationsTransformer
     */
    public function __construct(
        private readonly RepositoryInterface $productAssociationTypeRepository,
        private readonly DataTransformerInterface $productsToProductAssociationsTransformer,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this->productsToProductAssociationsTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entries' => $this->productAssociationTypeRepository->findAll(),
            'entry_name' => fn (ProductAssociationTypeInterface $productAssociationType) => $productAssociationType->getCode(),
            'entry_type' => ProductAutocompleteChoiceType::class,
            'entry_options' => fn (ProductAssociationTypeInterface $productAssociationType) => [
                'label' => $productAssociationType->getName(),
                'multiple' => true,
            ],
        ]);
    }

    public function getParent(): string
    {
        return FixedCollectionType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_product_associations';
    }
}
