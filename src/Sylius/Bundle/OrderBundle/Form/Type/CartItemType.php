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
use Sylius\Bundle\InventoryBundle\Validator\Constraints\InStock;
use Sylius\Bundle\InventoryBundle\Validator\Constraints\InStockValidator;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CartItemType extends AbstractResourceType
{
    public function __construct(
        string $dataClass,
        array $validationGroups,
        private DataMapperInterface $dataMapper,
        public EntityManagerInterface $entityManager,
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
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
                $newQuantity = $event->getData()['quantity'];
                $orderItem = $event->getForm()->getData();
                $oldQuantity = $orderItem->getQuantity();

                if (!$oldQuantity) {
                    return;
                }

                $variantId = $orderItem->getVariant()->getId();
                $variant = $this->entityManager->getRepository(ProductVariant::class)->findOneBy(['id' => $variantId]);
                $variantStock = $variant->getOnHand();

                if (false === $variant->isTracked()) {
                    return;
                }

                if ($newQuantity > $variantStock) {
                    $event->setData($oldQuantity);
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
