<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Installer\Checker;

use Sylius\Bundle\CoreBundle\Installer\Renderer\TableRenderer;
use Sylius\Bundle\CoreBundle\Installer\Requirement\Requirement;
use Sylius\Bundle\CoreBundle\Installer\Requirement\RequirementCollection;
use Sylius\Bundle\CoreBundle\Installer\Requirement\SyliusRequirements;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class SyliusRequirementsChecker implements RequirementsCheckerInterface
{
    /**
     * @var SyliusRequirements
     */
    private $syliusRequirements;

    /**
     * @var bool
     */
    private $fulfilled = true;

    /**
     * @param SyliusRequirements $syliusRequirements
     */
    public function __construct(SyliusRequirements $syliusRequirements)
    {
        $this->syliusRequirements = $syliusRequirements;
    }

    /**
     * {@inheritdoc}
     */
    public function check(InputInterface $input, OutputInterface $output)
    {
        $notFulfilledTable = new TableRenderer($output);
        $notFulfilledTable->setHeaders(['Requirement', 'Status']);

        $helpTable = new TableRenderer($output);
        $helpTable->setHeaders(['Issue', 'Recommendation']);

        foreach ($this->syliusRequirements as $collection) {
            $this->checkRequirementsInCollection($collection, $notFulfilledTable, $helpTable, $input->getOption('verbose'));
        }

        if (!$helpTable->isEmpty()) {
            $helpTable->render();
        }

        return $this->fulfilled;
    }

    /**
     * @param RequirementCollection $collection
     * @param TableRenderer $notFulfilledTable
     * @param TableRenderer $helpTable
     * @param mixed $verbose
     */
    private function checkRequirementsInCollection(
        RequirementCollection $collection,
        TableRenderer $notFulfilledTable,
        TableRenderer $helpTable,
        $verbose
    ) {
        /** @var Requirement $requirement */
        foreach ($collection as $requirement) {
            $label = $requirement->getLabel();

            if ($requirement->isFulfilled()) {
                $notFulfilledTable->addRow([$label, '<info>OK!</info>']);

                continue;
            }

            $notFulfilledTable->addRow([$label, $this->getRequirementRequiredMessage($requirement)]);
            $helpTable->addRow([$label, sprintf('<comment>%s</comment>', $requirement->getHelp())]);
        }

        if ($verbose || !$this->fulfilled) {
            $notFulfilledTable->setLabel($collection->getLabel());
            $notFulfilledTable->render();
        }
    }

    /**
     * @param Requirement $requirement
     *
     * @return string
     */
    private function getRequirementRequiredMessage(Requirement $requirement)
    {
        if ($requirement->isRequired()) {
            $this->fulfilled = false;

            return '<error>ERROR!</error>';
        }

        return '<comment>WARNING!</comment>';
    }
}
