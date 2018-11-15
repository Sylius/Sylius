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

namespace Sylius\Bundle\PromotionBundle\Form\Type\Action;

use Sylius\Bundle\PromotionBundle\Form\DataTransformer\PercentFloatToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;

final class PercentFloatType extends PercentType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new PercentFloatToLocalizedStringTransformer($options['scale'], $options['type']));
    }
}
