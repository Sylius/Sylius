<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContentBundle\Doctrine\ODM\PHPCR;

use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use Sylius\Bundle\ContentBundle\Repository\StaticContentRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\DocumentRepository;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StaticContentRepository extends DocumentRepository implements StaticContentRepositoryInterface
{
    /**
     * @var string
     */
    private $staticContentPath;

    /**
     * @param DocumentManagerInterface $dm
     * @param ClassMetadata $class
     * @param string $staticContentPath
     */
    public function __construct(DocumentManagerInterface $dm, ClassMetadata $class, $staticContentPath)
    {
        parent::__construct($dm, $class);

        $this->staticContentPath = $staticContentPath;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByName($name)
    {
        return $this->find($this->staticContentPath . '/' . $name);
    }
}
