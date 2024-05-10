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

use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionScopeType as BaseCatalogPromotionScopeType;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class CatalogPromotionScopeType extends AbstractType
{
    /** @var array<string, string> */
    private array $scopeConfigurationTypes;

    /**
     * @param iterable<string, object> $scopeConfigurationTypes
     */
    public function __construct(iterable $scopeConfigurationTypes)
    {
        foreach ($scopeConfigurationTypes as $type => $formType) {
            $this->scopeConfigurationTypes[$type] = $formType::class;
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('type', HiddenType::class);

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $this->addScopeToForm($event);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
                $this->addScopeToForm($event);
            })
        ;
    }

    public function getParent(): string
    {
        return BaseCatalogPromotionScopeType::class;
    }

    private function addScopeToForm(FormEvent $event): void
    {
        $data = $event->getData();

        if ($data === null) {
            return;
        }
        $form = $event->getForm();

        $dataType = $data instanceof CatalogPromotionScopeInterface ? $data->getType() : $data['type'];

        $scopeConfigurationType = $this->scopeConfigurationTypes[$dataType];
        $form->add('configuration', $scopeConfigurationType);
    }
}
