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

namespace Sylius\Component\Taxonomy\Generator;

use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface TaxonSlugGeneratorInterface
{
    /**
     * @param TaxonInterface $taxon
     * @param string|null $locale
     *
     * @return string
     */
    public function generate(TaxonInterface $taxon, ?string $locale = null): string;
}
