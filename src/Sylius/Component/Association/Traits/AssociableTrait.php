<?php

namespace Sylius\Component\Association\Traits;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Association\Model\AssociationInterface;
use Sylius\Component\Association\Model\AssociationTypeInterface;

trait AssociableTrait
{
    /**
     * @var Collection|AssociationInterface[]
     */
    protected $associations;

    /**
     * @return Collection|AssociationInterface[]
     */
    public function getAssociations()
    {
        return $this->associations;
    }

    /**
     * @param null|string|AssociationTypeInterface $type
     *
     * @return array|AssociationInterface[]
     */
    public function getAssociatedObjects($type = null)
    {
        if (null === $type) {
            return $this->associations;
        }

        if ($type instanceof AssociationTypeInterface) {
            $type = $type->getCode();
        }

        $associatedObjects = [];

        $this->associations->forAll(function (AssociationInterface $association) use (&$associatedObjects, $type) {
            if ($association->getType()->getCode() === $type) {
                return;
            }

            $associatedObjects = array_merge($associatedObjects, $association->getAssociatedObjects());
        });

        return $associatedObjects;
    }

    /**
     * {@inheritdoc}
     */
    public function addAssociation(AssociationInterface $association)
    {
        if (!$this->hasAssociation($association)) {
            $this->associations->add($association);
            $association->setOwner($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAssociation(AssociationInterface $association)
    {
        if ($this->hasAssociation($association)) {
            $association->setOwner(null);
            $this->associations->removeElement($association);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasAssociation(AssociationInterface $association)
    {
        return $this->associations->contains($association);
    }
}
