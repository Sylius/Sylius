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

namespace Sylius\Bundle\OrderBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CartItemType extends AbstractResourceType
{
    public function __construct(
        string $dataClass,
        array $validationGroups,
        private DataMapperInterface $dataMapper,
        public EntityManagerInterface $entityManager,
        public OrderItemQuantityModifierInterface $itemQuantityModifier
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'attr' => ['min' => 1],
                'label' => 'sylius.ui.quantity',
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
                $orderItem = $event->getData();

                if (!$orderItem) {
                    return;
                }

                $orderItemQuantity = $orderItem->getQuantity();

                $variantId = $orderItem->getVariant()->getId();
                $variant = $this->entityManager->getRepository(ProductVariant::class)->findOneBy(['id' => $variantId]);
                $variantStock = $variant->getOnHand();

                $uow = $this->entityManager->getUnitOfWork();
                $oldOrder = $uow->getOriginalEntityData($orderItem);
                $oldQuantity = $oldOrder['quantity'] ?? null;

                if (!$oldQuantity) {
                    return;
                }

                if ($orderItemQuantity > $variantStock) {
                    $this->itemQuantityModifier->modify($orderItem, $oldQuantity);
                }
            })
            ->setDataMapper($this->dataMapper)
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_cart_item';
    }
}
