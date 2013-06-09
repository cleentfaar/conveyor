<?php

/*
 * This file is part of the Conveyor package.
 *
 * (c) Jeroen Fiege <jeroen@webcreate.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webcreate\Conveyor\Repository;

use Webcreate\Conveyor\IO\IOInterface;
use Webcreate\Conveyor\Repository\Driver\DriverInterface;

class Repository
{
    protected $drivers = array();
    protected $driver;
    protected $type;
    protected $url;
    protected $io;

    public function __construct($type, $url, IOInterface $io = null, array $drivers = null)
    {
        $this->drivers = $drivers ? $drivers : array(
            'svn' => 'Webcreate\\Conveyor\\Repository\\Driver\\SvnDriver',
            'git' => 'Webcreate\\Conveyor\\Repository\\Driver\\GitDriver',
        );

        $this->io   = $io;
        $this->type = $type;
        $this->url  = $url;
    }

    /**
     * @return DriverInterface
     * @throws \RuntimeException
     */
    protected function getDriver()
    {
        if (isset($this->driver)) {
            return $this->driver;
        }

        if (isset($this->drivers[$this->type])) {
            $class = $this->drivers[$this->type];
            $this->driver = new $class($this->url, $this->io);
        }

        if (!$this->driver) {
            throw new \RuntimeException(sprintf('Driver for type \'%s\' not found', $this->type));
        }

        return $this->driver;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @todo move this to webcreate/vcs library
     *
     * @return string
     */
    public function getMasterBranch()
    {
        $branch = 'unknown';

        switch($this->getType()) {
            case "git":
                $branch = 'master';
                break;
            case "svn":
                $branch = 'trunk';
                break;
        }

        return $branch;
    }

    public function getVersions()
    {
        return $this->getDriver()->getVersions();
    }

    public function getVersion($name)
    {
        foreach ($this->getVersions() as $version) {
            if ($version->getName() === $name) {
                return $version;
            }
        }

        throw new \InvalidArgumentException(sprintf('Version \'%s\' not found', $name));
    }

    public function export(Version $version, $dest)
    {
        return $this->getDriver()->export($version, $dest);
    }

    /**
     * @param Version $oldVersion
     * @param Version $newVersion
     * @return \Webcreate\Vcs\Common\VcsFileInfo[]
     */
    public function diff(Version $oldVersion, Version $newVersion)
    {
        return $this->getDriver()->diff($oldVersion, $newVersion);
    }

    public function versionCompare($version1, $version2)
    {
        $build1 = $version1->getBuild();
        $build2 = $version2->getBuild();

        return $this->getDriver()->revisionCompare($build1, $build2);
    }

    public function changelog(Version $version1, Version $version2)
    {
        return $this->getDriver()->changelog($version1, $version2);
    }
}
