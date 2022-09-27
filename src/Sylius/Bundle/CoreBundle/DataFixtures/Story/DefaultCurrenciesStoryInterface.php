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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactoryInterface;
use Zenstruck\Foundry\Story;

interface DefaultCurrenciesStoryInterface extends StoryInterface
{
    public function getDefaultCurrencyCodes(): array;
}
