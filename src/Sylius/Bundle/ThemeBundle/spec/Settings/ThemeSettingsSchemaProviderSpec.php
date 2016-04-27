<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Settings;

use org\bovigo\vfs\vfsStream as VfsStream;
use org\bovigo\vfs\vfsStreamDirectory as VfsStreamDirectory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Settings\ThemeSettingsSchemaProvider;
use Sylius\Bundle\ThemeBundle\Settings\ThemeSettingsSchemaProviderInterface;

/**
 * @mixin ThemeSettingsSchemaProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeSettingsSchemaProviderSpec extends ObjectBehavior
{
    /**
     * @var VfsStreamDirectory
     */
    private $vfsStream;

    function let()
    {
        $this->vfsStream = VfsStream::setup();
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Settings\ThemeSettingsSchemaProvider');
    }

    function it_implements_theme_settings_schema_provider_interface()
    {
        $this->shouldImplement(ThemeSettingsSchemaProviderInterface::class);
    }

    function it_returns_valid_settings_schema(ThemeInterface $theme)
    {
        $settingsPath = $this->createSettingsFile(
<<<'PHP'
<?php

use Sylius\Bundle\SettingsBundle\Schema\CallbackSchema;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;

return new CallbackSchema(
    function (SettingsBuilderInterface $settingsBuilder) {

    },
    function (FormBuilderInterface $formBuilder) {

    }
);
PHP
        );

        $theme->getPath()->willReturn(dirname($settingsPath));

        $this->getSchema($theme)->shouldHaveType(SchemaInterface::class);
    }

    function it_throws_an_exception_if_settings_schema_is_of_incorrect_type(ThemeInterface $theme)
    {
        $settingsPath = $this->createSettingsFile(
<<<'PHP'
<?php

return new \stdClass();
PHP
        );

        $theme->getPath()->willReturn(dirname($settingsPath));

        $this
            ->shouldThrow(new \InvalidArgumentException(sprintf(
                'File "%s" must return an instance of "%s"',
                $settingsPath,
                SchemaInterface::class
            )))
            ->during('getSchema', [$theme])
        ;
    }

    function it_throws_an_exception_if_settings_schema_does_not_exist(ThemeInterface $theme)
    {
        $theme->getTitle()->willReturn('Candy shop');
        $theme->getName()->willReturn('candy/shop');

        $theme->getPath()->willReturn($this->vfsStream->url());

        $this
            ->shouldThrow(new \InvalidArgumentException(sprintf(
                'Could not find settings schema of theme "Candy shop" (candy/shop) in file "%s"',
                $this->vfsStream->url() . '/Settings.php'
            )))
            ->during('getSchema', [$theme])
        ;
    }

    /**
     * @param string $content
     *
     * @return string Created file URL
     */
    private function createSettingsFile($content)
    {
        $settingsFile = VfsStream::newFile('Settings.php');
        $settingsFile->setContent($content);

        $this->vfsStream->addChild($settingsFile);

        return $settingsFile->url();
    }
}
