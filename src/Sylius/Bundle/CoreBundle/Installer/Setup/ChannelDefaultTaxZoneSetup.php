<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Installer\Setup;

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

final readonly class ChannelDefaultTaxZoneSetup implements ChannelDefaultTaxZoneSetupInterface
{
    /**
     * @param RepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(
        private RepositoryInterface $channelRepository,
        private ObjectManager $channelManager,
    ) {
    }

    public function setup(
        ZoneInterface $zone,
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper,
    ): void {
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneBy([]);

        if (null === $channel) {
            return;
        }

        $question = new ConfirmationQuestion('Assign created zone as default tax zone? (yes/no) [no]: ', false);
        if (!$questionHelper->ask($input, $output, $question)) {
            return;
        }

        $channel->setDefaultTaxZone($zone);

        $this->channelManager->flush();
    }
}
