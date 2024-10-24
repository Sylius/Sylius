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

namespace spec\Sylius\Bundle\CoreBundle\Fixture\Listener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\FixturesBundle\Listener\SuiteEvent;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;
use Symfony\Component\Filesystem\Filesystem;

final class ImagesPurgerListenerSpec extends ObjectBehavior
{
    public function let(Filesystem $filesystem): void
    {
        $this->beConstructedWith($filesystem, '/media');
    }

    public function it_removes_images_before_fixture_suite(Filesystem $filesystem, SuiteInterface $suite): void
    {
        $filesystem->remove('/media')->shouldBeCalled();
        $filesystem->mkdir('/media')->shouldBeCalled();
        $filesystem->touch('/media/.gitkeep')->shouldBeCalled();

        $this->beforeSuite(new SuiteEvent($suite->getWrappedObject()), []);
    }
}
