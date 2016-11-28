<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Form\Type;

use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this);
    }

    /**
     * {@inheritdoc}
     */
    public function transform($coupon)
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
    public function reverseTransform($code)
    {
        if (null === $code || '' === $code) {
            return null;
        }

        return $this->promotionCouponRepository->findOneBy(['code' => $code]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
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
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_promotion_coupon_to_code';
    }
}
