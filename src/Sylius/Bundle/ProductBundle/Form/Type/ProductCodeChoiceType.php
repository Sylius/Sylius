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

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductCodeChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $productRepository;

    /**
     * @param RepositoryInterface $productRepository
     */
    public function __construct(RepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ReversedTransformer(new ResourceToIdentifierTransformer($this->productRepository, 'code')));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ProductChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_product_code_choice';
    }
}
