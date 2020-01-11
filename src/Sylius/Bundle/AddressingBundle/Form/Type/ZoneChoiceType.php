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

namespace Sylius\Bundle\AddressingBundle\Form\Type;

use Sylius\Component\Addressing\Model\Scope as AddressingScope;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ZoneChoiceType extends AbstractType
{
    /** @var RepositoryInterface */
    private $zoneRepository;

    /** @var string[] */
    private $scopeTypes;

    public function __construct(RepositoryInterface $zoneRepository, array $scopeTypes = [])
    {
        $this->zoneRepository = $zoneRepository;
        $this->scopeTypes = $scopeTypes;

        if (count($scopeTypes) === 0) {
            @trigger_error('Not passing scopeTypes thru constructor is deprecated in Sylius 1.5 and it will be removed in Sylius 2.0');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => function (Options $options): iterable {
                $zoneCriteria = [];
                if ($options['zone_scope'] !== AddressingScope::ALL) {
                    $zoneCriteria['scope'] = [$options['zone_scope'], AddressingScope::ALL];
                }

                return $this->zoneRepository->findBy($zoneCriteria);
            },
            'choice_value' => 'code',
            'choice_label' => 'name',
            'choice_translation_domain' => false,
            'label' => 'sylius.form.address.zone',
            'placeholder' => 'sylius.form.zone.select',
            'zone_scope' => AddressingScope::ALL,
        ]);

        $resolver->setAllowedValues('zone_scope', array_keys($this->scopeTypes));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_zone_choice';
    }
}
