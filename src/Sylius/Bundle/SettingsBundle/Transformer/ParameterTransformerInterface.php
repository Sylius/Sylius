<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Transformer;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ParameterTransformerInterface
{
    /**
     * Transform the parameter into format which is suitable for storage.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function transform($value);

    /**
     * Transform parameter value back into it's original form.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function reverseTransform($value);
}
