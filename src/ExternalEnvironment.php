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
     * @var string $branch The name for the selected branch
     */
    protected $branch;

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
     *
     * $options[<branch>]['env'] Filepath to source environment file.
     * $options[<branch>]['path'] Filepath to destination environment file.
     *
     * @param Builder $phpci
     * @param Build $build
     * @param array $options
     *
     */
    public function __construct(Builder $phpci, Build $build, array $options = [])
    {
        $this->phpci  = $phpci;
        $this->build  = $build;
        $this->branch = $this->build->getBranch();

        $this->envFilepath = $this->loadEnvPath($options);
        $this->envFilename = $this->loadFilename($options);
    }

    /**
     * Get the filepath to the source environment file.
     *
     * @param array $options
     * @return string
     * @throws \Exception
     */
    protected function loadEnvPath(array $options)
    {
        if (!is_array($options) || !isset($options[$this->branch])) {
            throw new \Exception('No configuration found for the ' . $this->branch . ' branch!');
        }

        if (!is_array($options[$this->branch]) || !isset($options[$this->branch]['env'])) {
            throw new \Exception('Please define a filepath to a environment file for the ' . $this->branch . ' branch.');
        }

        if (!file_exists($options[$this->branch]['env']) || !is_readable($options[$this->branch]['env'])) {
            throw new \Exception('Unable to load the environment file for the ' . $this->branch . '!');
        }

        return trim($options[$this->branch]['env']);
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

        if (!isset($options[$this->branch]['path'])) {
            return $filename;
        }

        $filename = $options[$this->branch]['path'];

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
            sprintf(
                'Copy external environment file %s for the %s branch to build directory',
                $this->envFilepath,
                $this->branch
            )
        );

        if (!copy($this->envFilepath, $destinationFilename)) {
            $this->phpci->logFailure('Copy error environment file to build directory!');

            return false;
        }

        $this->phpci->logSuccess('External environment file successful copied.');

        return true;
    }
}
