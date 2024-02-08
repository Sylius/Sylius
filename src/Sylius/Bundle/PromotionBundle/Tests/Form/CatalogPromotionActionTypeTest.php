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

namespace Sylius\Bundle\PromotionBundle\Tests\Form;

use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionActionType;
use Sylius\Component\Promotion\Model\CatalogPromotionAction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\DataCollector\DataCollectorExtension;
use Symfony\Component\Form\Extension\DataCollector\FormDataCollector;
use Symfony\Component\Form\Extension\DataCollector\FormDataExtractor;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Test\FormIntegrationTestCase;

final class CatalogPromotionActionTypeTest extends FormIntegrationTestCase
{
    /**
     * @test
     */
    public function it_allows_to_submit_form_with_wrong_values_without_throwing_an_exception_with_data_collector_enabled(): void
    {
        $this->expectNotToPerformAssertions();

        $catalogPromotionAction = new CatalogPromotionAction();
        $catalogPromotionAction->setType('configuration_type');
        $catalogPromotionAction->setConfiguration(['int_value' => 2]);

        $form = $this->factory->create(CatalogPromotionActionType::class, $catalogPromotionAction);

        $form->submit(['type' => 'configuration_type', 'configuration' => ['int_value' => '']]);
    }

    protected function getTypes(): array
    {
        return [
            new CatalogPromotionActionType(CatalogPromotionAction::class, [], [
                'configuration_type' => new ConfigurationType(),
            ]),
        ];
    }

    protected function getExtensions(): array
    {
        return [
            new DataCollectorExtension(new FormDataCollector(new FormDataExtractor())),
        ];
    }
}

final class ConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('int_value', IntegerType::class, [
                'required' => false,
            ])
        ;

        $builder
            ->get('int_value')
            ->resetViewTransformers()
            ->resetModelTransformers()
            ->addViewTransformer(new NumberToLocalizedStringTransformer())
        ;
    }
}
