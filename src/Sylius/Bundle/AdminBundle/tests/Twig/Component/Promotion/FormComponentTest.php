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

namespace Tests\Sylius\Bundle\AdminBundle\Twig\Component\Promotion;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Twig\Component\Promotion\FormComponent;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

final class FormComponentTest extends TestCase
{
    private const FORM_NAME = 'sylius_admin_promotion';

    private MockObject&RepositoryInterface $repository;

    private FormFactoryInterface&MockObject $formFactory;

    private PropertyAccessorInterface $propertyAccessor;

    private FormComponent $formComponent;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(RepositoryInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();

        $this->formComponent = new FormComponent(
            $this->repository,
            $this->formFactory,
            PromotionInterface::class,
            'promotion_form_type',
        );
        $this->formComponent->formName = self::FORM_NAME;
    }

    public function testItRestoresRuleConfigurationWhenTheSameTypeIsAddedAgainAfterRemoval(): void
    {
        $this->formComponent->formValues = [
            'rules' => [
                0 => ['type' => 'cart_quantity', 'configuration' => ['count' => 3]],
            ],
        ];

        $this->formComponent->removeCollectionItem($this->propertyAccessor, self::FORM_NAME . '[rules]', 0);

        $this->assertSame([], $this->formComponent->formValues['rules']);
        $this->assertSame(
            [['type' => 'cart_quantity', 'configuration' => ['count' => 3]]],
            $this->formComponent->deletedRules['cart_quantity'],
        );

        $this->formComponent->addCollectionItem($this->propertyAccessor, self::FORM_NAME . '[rules]', 'cart_quantity');

        $this->assertSame(
            ['type' => 'cart_quantity', 'configuration' => ['count' => 3]],
            $this->formComponent->formValues['rules'][0],
        );
        $this->assertArrayNotHasKey('cart_quantity', $this->formComponent->deletedRules);
    }

    public function testItRestoresActionConfigurationWhenTheSameTypeIsAddedAgainAfterRemoval(): void
    {
        $this->formComponent->formValues = [
            'actions' => [
                0 => ['type' => 'percentage_discount', 'configuration' => ['amount' => 0.1]],
            ],
        ];

        $this->formComponent->removeCollectionItem($this->propertyAccessor, self::FORM_NAME . '[actions]', 0);
        $this->formComponent->addCollectionItem($this->propertyAccessor, self::FORM_NAME . '[actions]', 'percentage_discount');

        $this->assertSame(
            ['type' => 'percentage_discount', 'configuration' => ['amount' => 0.1]],
            $this->formComponent->formValues['actions'][0],
        );
        $this->assertArrayNotHasKey('percentage_discount', $this->formComponent->deletedActions);
    }

    public function testItAddsAnEmptyRuleWhenNoMatchingRemovedRuleExistsForTheGivenType(): void
    {
        $this->formComponent->formValues = ['rules' => []];

        $this->formComponent->addCollectionItem($this->propertyAccessor, self::FORM_NAME . '[rules]', 'cart_quantity');

        $this->assertSame(['type' => 'cart_quantity'], $this->formComponent->formValues['rules'][0]);
    }

    public function testItDoesNotMixUpDeletedRulesAndDeletedActionsOfTheSameType(): void
    {
        $this->formComponent->formValues = [
            'rules' => [0 => ['type' => 'cart_quantity', 'configuration' => ['count' => 3]]],
            'actions' => [0 => ['type' => 'cart_quantity', 'configuration' => ['amount' => 0.1]]],
        ];

        $this->formComponent->removeCollectionItem($this->propertyAccessor, self::FORM_NAME . '[rules]', 0);
        $this->formComponent->removeCollectionItem($this->propertyAccessor, self::FORM_NAME . '[actions]', 0);

        $this->formComponent->addCollectionItem($this->propertyAccessor, self::FORM_NAME . '[rules]', 'cart_quantity');

        $this->assertSame(
            ['type' => 'cart_quantity', 'configuration' => ['count' => 3]],
            $this->formComponent->formValues['rules'][0],
        );
        $this->assertSame(
            [['type' => 'cart_quantity', 'configuration' => ['amount' => 0.1]]],
            $this->formComponent->deletedActions['cart_quantity'],
        );
    }

    public function testItRestoresEachRemovedRuleOfTheSameTypeInReverseOrderOfRemoval(): void
    {
        $this->formComponent->formValues = [
            'rules' => [
                0 => ['type' => 'cart_quantity', 'configuration' => ['count' => 3]],
                1 => ['type' => 'cart_quantity', 'configuration' => ['count' => 5]],
            ],
        ];

        $this->formComponent->removeCollectionItem($this->propertyAccessor, self::FORM_NAME . '[rules]', 0);
        $this->formComponent->removeCollectionItem($this->propertyAccessor, self::FORM_NAME . '[rules]', 1);

        $this->assertSame(
            [
                ['type' => 'cart_quantity', 'configuration' => ['count' => 3]],
                ['type' => 'cart_quantity', 'configuration' => ['count' => 5]],
            ],
            $this->formComponent->deletedRules['cart_quantity'],
        );

        $this->formComponent->addCollectionItem($this->propertyAccessor, self::FORM_NAME . '[rules]', 'cart_quantity');

        $this->assertSame(
            ['type' => 'cart_quantity', 'configuration' => ['count' => 5]],
            $this->formComponent->formValues['rules'][0],
        );
        $this->assertSame(
            [['type' => 'cart_quantity', 'configuration' => ['count' => 3]]],
            $this->formComponent->deletedRules['cart_quantity'],
        );

        $this->formComponent->addCollectionItem($this->propertyAccessor, self::FORM_NAME . '[rules]', 'cart_quantity');

        $this->assertSame(
            ['type' => 'cart_quantity', 'configuration' => ['count' => 3]],
            $this->formComponent->formValues['rules'][1],
        );
        $this->assertArrayNotHasKey('cart_quantity', $this->formComponent->deletedRules);
    }
}
