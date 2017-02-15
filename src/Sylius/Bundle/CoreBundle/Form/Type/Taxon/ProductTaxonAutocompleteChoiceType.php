<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Taxon;

use Sylius\Bundle\CoreBundle\Form\DataTransformer\ProductTaxonToTaxonTransformer;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\RecursiveTransformer;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ProductTaxonAutocompleteChoiceType extends AbstractType
{
    /**
     * @var FactoryInterface
     */
    private $productTaxonFactory;

    /**
     * @var RepositoryInterface
     */
    private $productTaxonRepository;

    /**
     * @param FactoryInterface $productTaxonFactory
     * @param RepositoryInterface $productTaxonRepository
     */
    public function __construct(FactoryInterface $productTaxonFactory, RepositoryInterface $productTaxonRepository)
    {
        $this->productTaxonFactory = $productTaxonFactory;
        $this->productTaxonRepository = $productTaxonRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder->addModelTransformer(
                new RecursiveTransformer(
                    new ProductTaxonToTaxonTransformer(
                        $this->productTaxonFactory,
                        $this->productTaxonRepository,
                        $options['product']
                    )
                )
            );
        }

        if (!$options['multiple']) {
            $builder->addModelTransformer(
                new ProductTaxonToTaxonTransformer(
                    $this->productTaxonFactory,
                    $this->productTaxonRepository,
                    $options['product']
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'resource' => 'sylius.taxon',
            'choice_name' => 'name',
            'choice_value' => 'code'
        ]);

        $resolver
            ->setRequired('product')
            ->setAllowedTypes('product', ProductInterface::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ResourceAutocompleteChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_product_taxon_autocomplete_choice';
    }
}
