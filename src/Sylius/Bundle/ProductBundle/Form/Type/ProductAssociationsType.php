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

use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ProductAssociationsType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $productAssociationTypeRepository;

    /**
     * @var DataTransformerInterface
     */
    protected $productsToProductAssociationsTransformer;

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
        $productAssociationTypes = $this->productAssociationTypeRepository->findAll();

        /** @var ProductAssociationTypeInterface $productAssociationType */
        foreach ($productAssociationTypes as $productAssociationType) {
            $builder->add($productAssociationType->getCode(), TextType::class, [
                'label' => $productAssociationType->getName(),
            ]);
        }

        $builder->addModelTransformer($this->productsToProductAssociationsTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product_associations';
    }
}
