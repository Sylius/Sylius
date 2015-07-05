<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Component\Taxonomy\Model\Taxonomy as BaseTaxonomy;

class Taxonomy extends BaseTaxonomy
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->setRoot(new Taxon());
    }

    /**
     * {@inheritdoc}
     */
    public static function getTranslationClass()
    {
        return 'Sylius\Component\Taxonomy\Model\TaxonomyTranslation';
    }
}
