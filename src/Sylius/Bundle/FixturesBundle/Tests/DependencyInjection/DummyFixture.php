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

namespace Sylius\Bundle\FixturesBundle\Tests\DependencyInjection;

use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;

class DummyFixture implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return '';
    }
}
