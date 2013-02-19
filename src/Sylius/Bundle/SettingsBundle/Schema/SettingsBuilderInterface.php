<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Schema;

use Sylius\Bundle\SettingsBundle\Transformer\ParameterTransformerInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Settings builder interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface SettingsBuilderInterface extends OptionsResolverInterface
{
    /**
     * Return all transformers.
     *
     * @return ParameterTransformerInterface[]
     */
    public function getTransformers();

    /**
     * Set transformer for given parameter.
     *
     * @param string                        $parameterName
     * @param ParameterTransformerInterface $transformer
     */
    public function setTransformer($parameterName, ParameterTransformerInterface $transformer);
}
