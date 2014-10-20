<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Builder;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Group data set builder.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class GroupBuilder extends AbstractBuilder
{
    /**
     * {@inheritdoc}
     */
    public function getDataSetDefault()
    {
        $groups = new ArrayCollection();
        $groups->add($this->build('Group 1'));
        $groups->add($this->build('Group 2'));
        $groups->add($this->buildWithFaker());

        return $groups;
    }

    public function getDataSetScenarioBlaBliBlo()
    {
        $groups = new ArrayCollection();
        $groups->add($this->build('Group 1 for Behat scenario BlaBliBlo'));
        $groups->add($this->build('Group 2 for Behat scenario BlaBliBlo'));

        return $groups;
    }

    /**
     * {@inheritdoc}
     */
    public function buildWithFaker()
    {
        return $this->buildWithData(array(
                'name' => $this->getFaker()->word(),
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