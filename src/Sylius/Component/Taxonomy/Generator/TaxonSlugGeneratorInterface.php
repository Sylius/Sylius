<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxonomy\Generator;

use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface TaxonSlugGeneratorInterface
{
    /**
     * @param TaxonInterface $taxon
     * @param string $locale
     *
     * @return string
     */
    public function generate(TaxonInterface $taxon, $locale);
}
