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

namespace Sylius\Bundle\AdminBundle\Form\Type;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\CoreBundle\Form\Type\Product\ProductImageType as BaseProductImageType;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductImageType extends AbstractType
{
    public function __construct(private readonly string $productVariantClass)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (isset($options['product']) && $options['product'] instanceof ProductInterface && $options['product']->getId() !== null) {
            $builder
                ->add('productVariants', EntityType::class, [
                    'class' => $this->productVariantClass,
                    'label' => 'sylius.ui.product_variants',
                    'multiple' => true,
                    'required' => false,
                    'choice_label' => 'descriptor',
                    'choice_value' => 'code',
                    'query_builder' => function (EntityRepository $er) use ($options): QueryBuilder {
                        return $er->createQueryBuilder('o')
                            ->where('o.product = :product')
                            ->setParameter('product', $options['product'])
                        ;
                    },
                    'autocomplete' => true,
                ])
            ;
        }
    }

    public function getParent(): string
    {
        return BaseProductImageType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_product_image';
    }
}
