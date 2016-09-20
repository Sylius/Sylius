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

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Component\Promotion\Model\CouponInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class CouponToCodeType extends AbstractType implements DataTransformerInterface
{
    /**
     * @var ObjectRepository
     */
    private $couponRepository;

    /**
     * @param ObjectRepository $couponRepository
     */
    public function __construct(ObjectRepository $couponRepository)
    {
        $this->couponRepository = $couponRepository;
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
                'label' => 'sylius.ui.code',
                'validation_groups' => function (FormInterface $form) {
                    $groups = ['sylius']; // Regular validation groups

                    if ((bool) $form->getData()) { // Validate the coupon if it was sent
                        $groups[] = 'sylius_promotion_coupon';
                    }

                    return $groups;
                }
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($coupon)
    {
        if (null === $coupon) {
            return '';
        }

        if (!$coupon instanceof CouponInterface) {
            throw new UnexpectedTypeException($coupon, CouponInterface::class);
        }

        return $coupon->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($code)
    {
        if (!$code) {
            return null;
        }

        return $this->couponRepository->findOneBy(['code' => $code]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_coupon_to_code';
    }
}
