<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\CoreBundle\Command\Model\PluginInfo;
use Sylius\Bundle\CoreBundle\Installer\Renderer\TableRenderer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ShowAvailablePluginsCommand extends Command
{
    /** @var Collection|PluginInfo[] */
    private $plugins;

    protected function configure(): void
    {
        $this->setName('sylius:show-available-plugins');
        $this->setDescription('Shows official Sylius Plugins');
        $this->configurePlugins();
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('<comment>Available official Sylius Plugins:</comment>');

        $pluginTable = new TableRenderer($output);
        $pluginTable->setHeaders(['Plugin', 'Description', 'URL']);

        foreach ($this->plugins as $plugin) {
            $pluginTable->addRow([sprintf('<info>%s</info>', $plugin->name()), $plugin->description(), $plugin->url()]);
        }

        $pluginTable->render();
    }

    private function configurePlugins(): void
    {
        $this->plugins = new ArrayCollection();

        /** @var PluginInfo[] */
        $this->plugins->add(new PluginInfo('<info>Admin Order Creation</info>', 'Creating (and copying) orders in the administration panel.', 'https://github.com/Sylius/AdminOrderCreationPlugin'));
        $this->plugins->add(new PluginInfo('<info>Customer Order Cancellation</info>', 'Allows customers to quickly cancel their unpaid and unshipped orders.', 'https://github.com/Sylius/CustomerOrderCancellationPlugin'));
        $this->plugins->add(new PluginInfo('<info>Customer Reorder</info>', 'Convenient reordering for the customers from the `My account` section.', 'https://github.com/Sylius/CustomerReorderPlugin'));
        $this->plugins->add(new PluginInfo('<info>Invoicing</info>', 'Automatised, basic invoicing system for orders.', 'https://github.com/Sylius/InvoicingPlugin'));
        $this->plugins->add(new PluginInfo('<info>RBAC</info>', 'Permissions management for the administration panel.', 'https://github.com/Sylius/RbacPlugin'));
        $this->plugins->add(new PluginInfo('<info>Refund</info>', 'Full and partial refunds of items and/or shipping costs including Credit Memos.', 'https://github.com/Sylius/RefundPlugin'));
    }
}
