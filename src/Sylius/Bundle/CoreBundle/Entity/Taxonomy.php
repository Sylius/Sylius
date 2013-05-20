<?php

namespace Sylius\Bundle\CoreBundle\Entity;

use Sylius\Bundle\TaxonomiesBundle\Entity\Taxonomy as BaseTaxonomy;
use Sylius\Bundle\CoreBundle\Model\ImageInterface;
use SplFileInfo;
use DateTime;

/**
 * Sylius core taxononomy entity.
 *
 */
class Taxonomy extends BaseTaxonomy implements ImageInterface
{

    /**
     * @var SplFileInfo
     */
    protected $file;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->setRoot(new Taxon());
        $this->createdAt = new DateTime();
    }

    public function hasFile()
    {
        return null !== $this->file;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(SplFileInfo $file)
    {
        $this->file = $file;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

}