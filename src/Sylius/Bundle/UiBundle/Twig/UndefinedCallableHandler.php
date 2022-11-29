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

namespace Sylius\Bundle\UiBundle\Twig;

use Twig\Error\SyntaxError;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class UndefinedCallableHandler
{
    /**
     * @var string[]
     */
    private const FUNCTION_COMPONENTS = [
        'sm_can' => 'winzou/state-machine-bundle',
        'sylius_grid_render_bulk_action' => 'sylius/grid-bundle',
        'sylius_grid_render_field' => 'sylius/grid-bundle',
        'sylius_grid_render_filter' => 'sylius/grid-bundle',
    ];

    /**
     * @var string[]
     */
    private const FILTER_COMPONENTS = [
        'sylius_currency_symbol' => 'sylius/currency-bundle',
        'sylius_locale_name' => 'sylius/locale-bundle',
        'imagine_filter' => 'liip/imagine-bundle',
    ];

    public static function onUndefinedFunction(string $name): TwigFunction|false
    {
        if (!isset(self::FUNCTION_COMPONENTS[$name])) {
            return false;
        }

        throw new SyntaxError(sprintf('Unknown function "%s". Did you forget to run "composer require %s"?', $name, self::FUNCTION_COMPONENTS[$name]));
    }

    public static function onUndefinedFilter(string $name): TwigFilter|false
    {
        if (!isset(self::FILTER_COMPONENTS[$name])) {
            return false;
        }

        throw new SyntaxError(sprintf('Unknown filter "%s". Did you forget to run "composer require %s"?', $name, self::FILTER_COMPONENTS[$name]));
    }
}
