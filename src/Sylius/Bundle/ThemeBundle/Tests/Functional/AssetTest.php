<?php

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
     * @param integer $symlinkMask
     */
    public function testAssets($symlinkMask)
    {
        $webDirectory = $this->getTmpDirPath(self::TEST_CASE) . '/web';
        if (!is_dir($webDirectory)) {
            mkdir($webDirectory, 0777, true);
        }

        chdir($webDirectory);

        $client = $this->getClient();

        $client->getContainer()->get('sylius.theme.asset.assets_installer')->installAssets($webDirectory, $symlinkMask);

        $crawler = $client->request('GET', '/template/:Asset:assetsTest.txt.twig');
        $lines = explode("\n", $crawler->text());
        foreach ($lines as $line) {
            list($expectedText, $assetFile) = explode(": ", $line);

            $contents = file_get_contents($webDirectory . $assetFile);

            $this->assertEquals($expectedText, $contents);
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
