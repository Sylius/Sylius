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

use Sylius\Bundle\ResourceBundle\Form\Type\FixedCollectionType;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductAssociationsType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $productAssociationTypeRepository;

    /**
     * @var DataTransformerInterface
     */
    private $productsToProductAssociationsTransformer;

    /**
     * @param RepositoryInterface $productAssociationTypeRepository
     * @param DataTransformerInterface $productsToProductAssociationsTransformer
     */
    public function __construct(
        RepositoryInterface $productAssociationTypeRepository,
        DataTransformerInterface $productsToProductAssociationsTransformer
    ) {
        $this->productAssociationTypeRepository = $productAssociationTypeRepository;
        $this->productsToProductAssociationsTransformer = $productsToProductAssociationsTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->productsToProductAssociationsTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'entries' => $this->productAssociationTypeRepository->findAll(),
            'entry_type' => TextType::class,
            'entry_name' => function (ProductAssociationTypeInterface $productAssociationType) {
                return $productAssociationType->getCode();
            },
            'entry_options' => function (ProductAssociationTypeInterface $productAssociationType) {
                return [
                    'label' => $productAssociationType->getName(),
                ];
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return FixedCollectionType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_product_associations';
    }
}
