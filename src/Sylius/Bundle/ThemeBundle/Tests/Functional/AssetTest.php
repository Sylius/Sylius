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

namespace Sylius\Bundle\ThemeBundle\Tests\Functional;

use Sylius\Bundle\ThemeBundle\Asset\Installer\AssetsInstallerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AssetTest extends WebTestCase
{
    protected function tearDown()
    {
        parent::tearDown();

        file_put_contents(__DIR__ . '/../Fixtures/themes/FirstTestTheme/TestBundle/public/theme_asset.txt', 'Theme asset' . PHP_EOL);
    }

    /**
     * @test
     * @dataProvider getSymlinkMasks
     *
     * @param int $symlinkMask
     */
    public function it_dumps_assets($symlinkMask): void
    {
        $client = self::createClient();

        $webDirectory = $this->createWebDirectory();

        $client->getContainer()->get('sylius.theme.asset.assets_installer')->installAssets($webDirectory, $symlinkMask);

        $crawler = $client->request('GET', '/template/:Asset:assetsTest.txt.twig');
        $lines = explode("\n", $crawler->text());

        $this->assertFileContent($lines, $webDirectory);
    }

    /**
     * @test
     * @dataProvider getSymlinkMasks
     *
     * @param int $symlinkMask
     */
    public function it_updates_dumped_assets_if_they_are_modified($symlinkMask): void
    {
        $client = self::createClient();

        $webDirectory = $this->createWebDirectory();

        $client->getContainer()->get('sylius.theme.asset.assets_installer')->installAssets($webDirectory, $symlinkMask);

        sleep(1);
        file_put_contents(__DIR__ . '/../Fixtures/themes/FirstTestTheme/TestBundle/public/theme_asset.txt', 'Theme asset modified');
        clearstatcache();

        $client->getContainer()->get('sylius.theme.asset.assets_installer')->installAssets($webDirectory, $symlinkMask);

        $crawler = $client->request('GET', '/template/:Asset:modifiedAssetsTest.txt.twig');
        $lines = explode("\n", $crawler->text());

        $this->assertFileContent($lines, $webDirectory);
    }

    /**
     * @test
     * @dataProvider getSymlinkMasks
     *
     * @param int $symlinkMask
     */
    public function it_dumps_assets_correctly_even_if_nothing_has_changed($symlinkMask): void
    {
        $client = self::createClient();

        $webDirectory = $this->createWebDirectory();

        $client->getContainer()->get('sylius.theme.asset.assets_installer')->installAssets($webDirectory, $symlinkMask);
        $client->getContainer()->get('sylius.theme.asset.assets_installer')->installAssets($webDirectory, $symlinkMask);

        $crawler = $client->request('GET', '/template/:Asset:assetsTest.txt.twig');
        $lines = explode("\n", $crawler->text());

        $this->assertFileContent($lines, $webDirectory);
    }

    private function createWebDirectory()
    {
        $webDirectory = self::$kernel->getCacheDir() . '/web';
        if (!is_dir($webDirectory)) {
            mkdir($webDirectory, 0777, true);
        }

        chdir($webDirectory);

        return $webDirectory;
    }

    private function assertFileContent($lines, $webDirectory): void
    {
        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }

            [$expectedText, $assetFile] = explode(': ', $line);

            $contents = file_get_contents($webDirectory . $assetFile);

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
