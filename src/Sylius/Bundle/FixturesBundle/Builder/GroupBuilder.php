<?php

namespace Sylius\Bundle\FixturesBundle\Builder;

use Doctrine\Common\Collections\ArrayCollection;

class GroupBuilder extends AbstractBuilder
{
    public function getResourceClass()
    {
        return 'Sylius\\Bundle\\CoreBundle\\Model\\Group';
    }

    public function getSetDefault()
    {
        $groups = new ArrayCollection();
        $groups->add($this->build('Group 1'));
        $groups->add($this->build('Group 2'));
        $groups->add($this->buildWithFaker());

        return $groups;
    }

    public function getSetScenarioBlaBliBlo()
    {
        $groups = new ArrayCollection();
        $groups->add($this->build('Group 1 for Behat scenario BlaBliBlo'));
        $groups->add($this->build('Group 2 for Behat scenario BlaBliBlo'));

        return $groups;
    }

    public function buildWithFaker()
    {
        return $this->buildWithData(array(
                'name' => $this->faker->word(),
            )
        );
    }

    protected function build($name)
    {
        return $this->buildWithData(array(
                'name' => $name,
            )
        );
    }

} 