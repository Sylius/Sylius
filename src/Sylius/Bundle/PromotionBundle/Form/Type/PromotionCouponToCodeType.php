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
    /**
     * @var RepositoryInterface
     */
    private $promotionCouponRepository;

    /**
     * @param RepositoryInterface $promotionCouponRepository
     */
    public function __construct(RepositoryInterface $promotionCouponRepository)
    {
        $this->promotionCouponRepository = $promotionCouponRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this);
    }

    /**
     * {@inheritdoc}
     */
    public function transform($coupon): string
    {
        if (null === $coupon) {
            return '';
        }

        if (!$coupon instanceof PromotionCouponInterface) {
            throw new UnexpectedTypeException($coupon, PromotionCouponInterface::class);
        }

        return $coupon->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($code): ?PromotionCouponInterface
    {
        if (null === $code || '' === $code) {
            return null;
        }

        return $this->promotionCouponRepository->findOneBy(['code' => $code]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
                'label' => 'sylius.ui.code',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_promotion_coupon_to_code';
    }
}
