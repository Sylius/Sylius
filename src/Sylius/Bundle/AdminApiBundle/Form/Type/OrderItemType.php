<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminApiBundle\Form\Type;

use Sylius\Bundle\OrderBundle\Form\Type\OrderItemType as BaseOrderItemType;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.org>
 */
final class OrderItemType extends AbstractType
{
    /**
     * @var ProductVariantRepositoryInterface
     */
    private $variantRepository;

    /**
     * @param ProductVariantRepositoryInterface $variantRepository
     */
    public function __construct(ProductVariantRepositoryInterface $variantRepository)
    {
        $this->variantRepository = $variantRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('variant', TextType::class, [
            'constraints' => [
                new NotBlank(['groups' => ['sylius']]),
            ],
        ]);

        $builder->get('variant')->addModelTransformer(new ResourceToIdentifierTransformer($this->variantRepository, 'code'));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $orderItem = $event->getData();

            Assert::notNull($orderItem);

            if (null !== $orderItem->getId()) {
                $form = $event->getForm();

                $form->remove('variant');
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return BaseOrderItemType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_admin_api_order_item';
    }
}
