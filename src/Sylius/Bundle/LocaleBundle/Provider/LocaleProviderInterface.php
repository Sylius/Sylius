<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Provider;

use A2lix\TranslationFormBundle\Locale\LocaleProviderInterface as FormLocaleProviderInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface as BaseLocaleProviderInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface LocaleProviderInterface extends BaseLocaleProviderInterface, FormLocaleProviderInterface
{
}
