<?php
/**
 * External Environment plugin for PHPCI
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright Copyright 2015, Chris van Daele
 * @license https://github.com/rephluX/phpci-external-environment/blob/master/LICENSE
 * @link https://github.com/rephluX/phpci-external-environment
 */

namespace Rephlux\PHPCI\Plugin;

use PHPCI\Plugin;
use PHPCI\Builder;
use PHPCI\Model\Build;
use PHPCI\ZeroConfigPlugin;

/**
 * ExternalEnvironment - Copies a specified environment file to current build.
 *
 * @author       Chris van Daele <engine_no9@gmx.net>
 * @package      Rephlux
 * @subpackage   PHPCI\Plugins
 */
class ExternalEnvironment implements Plugin, ZeroConfigPlugin
{
    /**
     * @var \PHPCI\Builder
     */
    protected $phpci;

    /**
     * @var \PHPCI\Model\Build
     */
    protected $build;

    /**
     * @var string $envFilepath The filepath to the env file.
     */
    protected $envFilepath = false;

    /**
     * @var string $enFilename The filename for the env file.
     */
    protected $envFilename = '';

    /**
     * Check if this plugin can be executed.
     *
     * @param $stage
     * @param Builder $builder
     * @param Build $build
     *
     * @return bool
     */
    public static function canExecute($stage, Builder $builder, Build $build)
    {
        if ($stage == 'setup') {
            return true;
        }

        return false;
    }

    /**
     * Standard Constructor
     * $options['env'] Filepath to source environment file.
     * $options['path'] Filepath to destination environment file.
     *
     * @param Builder $phpci
     * @param Build $build
     * @param array $options
     *
     */
    public function __construct(Builder $phpci, Build $build, array $options = [])
    {
        $this->phpci = $phpci;
        $this->build = $build;

        $this->envFilepath = $this->loadEnvPath($options);
        $this->envFilename = $this->loadFilename($options);
    }

    /**
     * Get the filepat to the source environment file.
     *
     * @param array $options
     * @return string
     * @throws \Exception
     */
    protected function loadEnvPath(array $options)
    {
        if (!is_array($options) || !isset($options['env'])) {
            throw new \Exception('Please define a filepath to a environment file for a environment application!');
        }

        if (!file_exists($options['env']) || !is_readable($options['env'])) {
            throw new \Exception('Please define a valid filepath or check permissions to specified environment file!');
        }

        return trim($options['env']);
    }

    /**
     * The name for the source environment file.
     *
     * @param array $options
     * @return string
     * @throws \Exception
     */
    protected function loadFilename(array $options)
    {
        $filename = '.env';

        if (!isset($options['path'])) {
            return $filename;
        }

        $filename = $options['path'];

        if (!preg_match('/^[a-z0-9-_\.\/]+$/', $filename)) {
            throw new \Exception('Please define a valid filename for the destination environment filename!');
        }

        return $filename;
    }

    /**
     * Runs the copy command.
     *
     * @return bool
     */
    public function execute()
    {
        $destinationFilename = $this->phpci->buildPath . '/' . $this->envFilename;

        $this->phpci->log(
            sprintf('Copy external environment file %s to build directory', $this->envFilepath)
        );

        if (!copy($this->envFilepath, $destinationFilename)) {
            $this->phpci->logFailure('Copy error environment file to build directory!');

            return false;
        }

        $this->phpci->logSuccess('External environment file successful copied.');

        return true;
    }
}