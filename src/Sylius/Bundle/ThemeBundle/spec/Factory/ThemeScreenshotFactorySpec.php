<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Factory\ThemeScreenshotFactory;
use Sylius\Bundle\ThemeBundle\Factory\ThemeScreenshotFactoryInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeScreenshot;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeScreenshotFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ThemeScreenshotFactory::class);
    }

    function it_implements_theme_screenshot_factory_interface()
    {
        $this->shouldImplement(ThemeScreenshotFactoryInterface::class);
    }

    function it_creates_a_screenshot_from_an_array()
    {
        $this
            ->createFromArray(['path' => '/screenshot/path.jpg', 'title' => 'Steamboat', 'description' => 'With steamboat into a wonderful cruise'])
            ->shouldBeScreenshotWithTheFollowingProperties(['path' => '/screenshot/path.jpg', 'title' => 'Steamboat', 'description' => 'With steamboat into a wonderful cruise'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchers()
    {
        return [
            'beScreenshotWithTheFollowingProperties' => function (ThemeScreenshot $subject, array $properties) {
                if (isset($properties['path']) && $subject->getPath() !== $properties['path']) {
                    return false;
                }

                if (isset($properties['title']) && $subject->getTitle() !== $properties['title']) {
                    return false;
                }

                if (isset($properties['description']) && $subject->getDescription() !== $properties['description']) {
                    return false;
                }

                return true;
            },
        ];
    }
}
