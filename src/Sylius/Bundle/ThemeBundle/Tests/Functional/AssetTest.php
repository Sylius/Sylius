<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Tests\Functional;

use Sylius\Bundle\ThemeBundle\Asset\Installer\AssetsInstallerInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class AssetTest extends ThemeBundleTestCase
{
    /**
     * @dataProvider getSymlinkMasks
     *
     * @param int $symlinkMask
     */
    public function testAssets($symlinkMask)
    {
        $webDirectory = $this->getTmpDirPath(self::TEST_CASE).'/web';
        if (!is_dir($webDirectory)) {
            mkdir($webDirectory, 0777, true);
        }

        chdir($webDirectory);

        $client = $this->getClient();

        $client->getContainer()->get('sylius.theme.asset.assets_installer')->installAssets($webDirectory, $symlinkMask);

        $crawler = $client->request('GET', '/template/:Asset:assetsTest.txt.twig');
        $lines = explode("\n", $crawler->text());
        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }

            list($expectedText, $assetFile) = explode(': ', $line);

            $contents = file_get_contents($webDirectory.$assetFile);

            $this->assertEquals($expectedText, trim($contents));
        }
    }

    /**
     * @return array
     */
    public function getSymlinkMasks()
    {
        return [
            [AssetsInstallerInterface::RELATIVE_SYMLINK],
            [AssetsInstallerInterface::SYMLINK],
            [AssetsInstallerInterface::HARD_COPY],
        ];
    }
}
