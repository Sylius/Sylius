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

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\AddressingBundle\Form\Type\ZoneChoiceType;
use Sylius\Bundle\TaxationBundle\Form\Type\TaxRateType;
use Sylius\Component\Core\Model\Scope;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class TaxRateTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('zone', ZoneChoiceType::class, ['zone_scope' => Scope::TAX]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [TaxRateType::class];
    }
}
