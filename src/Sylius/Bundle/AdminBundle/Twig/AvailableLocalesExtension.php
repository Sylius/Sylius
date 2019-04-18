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

namespace Sylius\Bundle\AdminBundle\Twig;

use Sylius\Bundle\AdminBundle\Templating\Helper\AvailableLocaleHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AvailableLocalesExtension extends AbstractExtension
{
    /** @var AvailableLocaleHelper */
    private $localesProvider;

    public function __construct(AvailableLocaleHelper $localesProvider)
    {
        $this->localesProvider = $localesProvider;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('sylius_available_locales',[$this->localesProvider, 'getDefinedLocaleCodes']),
        ];
    }
}
