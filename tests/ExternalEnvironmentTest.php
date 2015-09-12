<?php

use Rephlux\PHPCI\Plugin;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use Rephlux\PHPCI\Plugin\ExternalEnvironment;

class ExternalEnvironmentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ExternalEnvironment
     */
    protected $plugin = false;

    /**
     * @var \PHPCI\Model\Build
     */
    protected $buildMock = false;

    /**
     * @var \PHPCI\Builder
     */
    protected $builderMock = false;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->builderMock = $this->getMockBuilder('PHPCI\Builder')->getMock();
        $this->buildMock   = $this->getMockBuilder('PHPCI\Model\Build')->getMock();

        $this->builderMock
            ->method('log')
            ->willReturn(null);

        $this->builderMock
            ->method('logSuccess')
            ->willReturn(null);

        $this->builderMock
            ->method('logFailure')
            ->willReturn(null);
    }

    /**
     * Tear down method
     *
     * This method will destroy the instance of the plugin.
     */
    public function tearDown()
    {
        parent::tearDown();

        $this->plugin = null;
    }

    /**
     * Ensure that the plugin can be executed.
     *
     */
    public function testPluginCanBeExecuted()
    {
        $setup = ExternalEnvironment::canExecute('setup', $this->builderMock, $this->buildMock);

        $this->assertEquals(true, $setup);
    }

    /**
     * Ensure that the plugin can be instantiated with valid values.
     *
     */
    public function testPluginCanBeInstantiated()
    {
        $options = ['env' => dirname(__DIR__) . '/tests/stubs/sample.env'];
        $plugin  = $this->getPlugin($options);

        $this->assertInstanceOf('Rephlux\PHPCI\Plugin\ExternalEnvironment', $plugin);
    }

    /**
     * Ensure that the plugin can not be instantiated with a missing env path.
     *
     * @expectedException Exception
     * @expectedExceptionMessage Please define a filepath
     */
    public function testPluginCanNotBeInstantiatedWithMissingEnvPath()
    {
        $options = [];

        $this->getPlugin($options);
    }

    /**
     * Ensure that the plugin can not be instantiated with a invalid env path.
     *
     * @expectedException Exception
     * @expectedExceptionMessage Please define a valid filepath
     */
    public function testPluginCanNotBeInstantiatedWithInvalidEnvPath()
    {
        $options = ['env' => dirname(__DIR__) . '/tests/stubs/missing.env'];

        $this->getPlugin($options);
    }

    /**
     * Ensure that the plugin can not be instantiated with a invalid path setting.
     *
     * @expectedException Exception
     * @expectedExceptionMessage Please define a valid filename
     */
    public function testPluginCanNotBeInstantiatedWithInvalidPathSetting()
    {
        $options = ['env' => dirname(__DIR__) . '/tests/stubs/sample.env', 'path' => 'invalid%file(path'];

        $this->getPlugin($options);
    }

    /**
     * Ensure that the plugin copies a specified environment file to specified path.
     *
     */
    public function testCopyEnvironmentFile()
    {
        vfsStream::setup('tmp');

        $path   = '.env';
        $plugin = $this->getPlugin(['env' => dirname(__DIR__) . '/tests/stubs/sample.env']);

        $this->executePlugin($plugin, $path);
    }

    /**
     * Ensure that the plugin copies a specified environment file to specified path.
     *
     * To specify a custom destination filename, the path setting is being provided in the options.
     */
    public function testCopyEnvironmentFileWithPathSetting()
    {
        vfsStream::setup('tmp/conf');

        $path   = 'conf/dummy.env';
        $plugin = $this->getPlugin(['env' => dirname(__DIR__) . '/tests/stubs/sample.env', 'path' => $path]);

        $this->executePlugin($plugin, $path);
    }

    /**
     * Execute the plugin.
     *
     * @param ExternalEnvironment $plugin
     * @param $path
     */
    protected function executePlugin(ExternalEnvironment $plugin, $path)
    {
        $this->builderMock->buildPath = vfsStream::url('tmp');

        $directory = vfsStreamWrapper::getRoot();

        $this->assertFalse($directory->hasChild($path));
        $this->assertTrue($plugin->execute());
        $this->assertTrue($directory->hasChild($path));

        $this->assertSame('APP_ENV=local', file_get_contents($directory->getChild($path)->url()));
    }

    /**
     * Get a plugin instance.
     *
     * @param array $options
     * @return ExternalEnvironment
     */
    protected function getPlugin(array $options)
    {
        $this->plugin = new ExternalEnvironment($this->builderMock, $this->buildMock, $options);

        return $this->plugin;
    }
}