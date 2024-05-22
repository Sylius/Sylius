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

use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionActionType as BaseCatalogPromotionActionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Twig\Environment;

final class CatalogPromotionActionType extends AbstractType
{
    /** @var array<string, string> */
    private array $actionTypes = [];

    /** @var array<string, string> */
    private array $actionConfigurationTypes;

    /**
     * @param iterable<string, object> $actionConfigurationTypes
     */
    public function __construct(iterable $actionConfigurationTypes, private Environment $twig)
    {
        foreach ($actionConfigurationTypes as $type => $formType) {
            $this->actionConfigurationTypes[$type] = $formType::class;
            $this->actionTypes['sylius.form.catalog_promotion.action.' . $type] = $type;
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'sylius.ui.type',
                'choices' => $this->actionTypes,
                'choice_attr' => function (?string $type) use ($builder): array {
                    return [
                        'data-configuration' => $this->twig->render(
                            '@SyliusAdmin/CatalogPromotion/_action.html.twig',
                            ['field' => $builder->create(
                                'configuration',
                                $this->actionConfigurationTypes[$type],
                                ['label' => false, 'csrf_protection' => false],
                            )->getForm()->createView()],
                        ),
                    ];
                },
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_catalog_promotion_action';
    }

    public function getParent(): string
    {
        return BaseCatalogPromotionActionType::class;
    }
}
