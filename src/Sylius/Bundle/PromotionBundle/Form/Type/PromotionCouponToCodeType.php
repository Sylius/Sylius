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

namespace Sylius\Bundle\PromotionBundle\Form\Type;

use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PromotionCouponToCodeType extends AbstractType implements DataTransformerInterface
{
    public function __construct(private RepositoryInterface $promotionCouponRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this);
    }

    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        if (!$value instanceof PromotionCouponInterface) {
            throw new UnexpectedTypeException($value, PromotionCouponInterface::class);
        }

        return $value->getCode();
    }

    public function reverseTransform($value): ?PromotionCouponInterface
    {
        if (null === $value || '' === $value) {
            return null;
        }

        return $this->promotionCouponRepository->findOneBy(['code' => $value]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
                'label' => 'sylius.ui.code',
            ])
        ;
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_promotion_coupon_to_code';
    }
}
