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

namespace Sylius\Bundle\ThemeBundle\Templating\Locator;

use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

interface TemplateLocatorInterface
{
    /**
     * @param TemplateReferenceInterface $template
     * @param ThemeInterface $theme
     *
     * @return string
     *
     * @throws ResourceNotFoundException
     */
    public function locateTemplate(TemplateReferenceInterface $template, ThemeInterface $theme): string;
}
