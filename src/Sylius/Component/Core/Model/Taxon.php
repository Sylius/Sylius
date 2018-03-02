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

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Taxonomy\Model\Taxon as BaseTaxon;
use Sylius\Component\Taxonomy\Model\TaxonTranslation;

class Taxon extends BaseTaxon implements TaxonInterface
{
    use TimestampableTrait;
    
    use FilesAwareTrait;
    
    public function __construct()
    {
        parent::__construct();

        $this->createdAt = new \DateTime();
        $this->files = new ArrayCollection();
    }
    
    /**
     * {@inheritdoc}
     */
    public static function getTranslationClass(): string
    {
        return TaxonTranslation::class;
    }
}
