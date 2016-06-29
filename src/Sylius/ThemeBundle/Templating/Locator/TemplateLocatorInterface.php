<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ThemeBundle\Templating\Locator;

use Sylius\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
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
    public function locateTemplate(TemplateReferenceInterface $template, ThemeInterface $theme);
}
