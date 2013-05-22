<?php

namespace Sylius\Bundle\CoreBundle\Entity;

use Sylius\Bundle\CoreBundle\Model\ImageTaxonInterface;
use Sylius\Bundle\TaxonomiesBundle\Entity\Taxon as BaseTaxon;
use SplFileInfo;
use DateTime;

/**
 * Sylius core taxon entity.
 *
 */
class Taxon extends BaseTaxon implements ImageTaxonInterface
{

    /**
     * @var SplFileInfo
     */
    protected $imageFile;

    /**
     * @var string
     */
    protected $imagePath;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    public function __construct()
    {
        parent::__construct();

        $this->createdAt = new DateTime();
    }

    public function hasImageFile()
    {
        return null !== $this->imageFile;
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImageFile(SplFileInfo $imageFile)
    {
        $this->imageFile = $imageFile;
    }

    public function getImagePath()
    {
        return $this->imagePath;
    }

    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;
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