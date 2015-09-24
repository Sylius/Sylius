<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Metadata\Model\Custom\PageMetadataInterface;
use Sylius\Component\Metadata\Model\RootMetadataInterface;

class MetadataRepository extends EntityRepository
{
    /**
     * @param string $id
     *
     * @return PageMetadataInterface
     */
    public function createPageMetadataWithGivenId($id)
    {
        /** @var RootMetadataInterface $rootMetadata */
        $rootMetadata = new $this->_entityName();
        $rootMetadata->setId($id);

        return $rootMetadata;
    }
}
