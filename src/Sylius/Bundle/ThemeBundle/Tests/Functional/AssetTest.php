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
final class AssetTest extends ThemeBundleTestCase
{
    protected function tearDown()
    {
        parent::tearDown();

        file_put_contents(__DIR__.'/../Fixtures/themes/FirstTestTheme/TestBundle/public/theme_asset.txt', 'Theme asset'.PHP_EOL);
    }

    /**
     * @dataProvider getSymlinkMasks
     *
     * @param int $symlinkMask
     */
    public function testAssets($symlinkMask)
    {
        $webDirectory = $this->createWebDirectory();

        $client = $this->getClient();

        $client->getContainer()->get('sylius.theme.asset.assets_installer')->installAssets($webDirectory, $symlinkMask);

        $crawler = $client->request('GET', '/template/:Asset:assetsTest.txt.twig');
        $lines = explode("\n", $crawler->text());

        $this->assertFileContent($lines, $webDirectory);
    }

    /**
     * @dataProvider getSymlinkMasks
     *
     * @param int $symlinkMask
     */
    public function testAssetsWhenModifiedAndReinstalled($symlinkMask)
    {
        $webDirectory = $this->createWebDirectory();

        $client = $this->getClient();

        $client->getContainer()->get('sylius.theme.asset.assets_installer')->installAssets($webDirectory, $symlinkMask);

        sleep(1);
        file_put_contents(__DIR__.'/../Fixtures/themes/FirstTestTheme/TestBundle/public/theme_asset.txt', 'Theme asset modified');
        clearstatcache();

        $client->getContainer()->get('sylius.theme.asset.assets_installer')->installAssets($webDirectory, $symlinkMask);

        $crawler = $client->request('GET', '/template/:Asset:modifiedAssetsTest.txt.twig');
        $lines = explode("\n", $crawler->text());

        $this->assertFileContent($lines, $webDirectory);
    }

    /**
     * @dataProvider getSymlinkMasks
     *
     * @param int $symlinkMask
     */
    public function testAssetsWhenReinstalled($symlinkMask)
    {
        $webDirectory = $this->createWebDirectory();

        $client = $this->getClient();

        $client->getContainer()->get('sylius.theme.asset.assets_installer')->installAssets($webDirectory, $symlinkMask);
        $client->getContainer()->get('sylius.theme.asset.assets_installer')->installAssets($webDirectory, $symlinkMask);

        $crawler = $client->request('GET', '/template/:Asset:assetsTest.txt.twig');
        $lines = explode("\n", $crawler->text());

        $this->assertFileContent($lines, $webDirectory);
    }

    private function createWebDirectory()
    {
        $webDirectory = $this->getTmpDirPath(self::TEST_CASE).'/web';
        if (!is_dir($webDirectory)) {
            mkdir($webDirectory, 0777, true);
        }

        chdir($webDirectory);

        return $webDirectory;
    }

    private function assertFileContent($lines, $webDirectory)
    {
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
