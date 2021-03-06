<?php

namespace Webcreate\Conveyor\Task;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Webcreate\Conveyor\IO\IOInterface;
use Webcreate\Conveyor\Repository\Version;
use Webcreate\Conveyor\Task\Result\ExecuteResult;
use Webcreate\Conveyor\Util\FileCollection;

class RemoveTask extends Task
{
    protected $cwd;

    /**
     * @var IOInterface
     */
    protected $io;

    /**
     * @param $cwd
     */
    function __construct($cwd)
    {
        $this->cwd = $cwd;
    }

    /**
     * @param IOInterface $io
     *
     * @return $this
     */
    public function setIo(IOInterface $io)
    {
        $this->io = $io;

        return $this;
    }

    /**
     * @param string $target
     * @param Version $version
     *
     * @return ExecuteResult
     */
    public function execute($target, Version $version)
    {
        $filesystem = new Filesystem();

        if ($this->io && !$this->io->isVerbose()) {
            $this->output(sprintf('Removing: <comment>0%%</comment>'));
        }

        $collection = new FileCollection($this->cwd);

        if (!$this->options['files'] && $this->options['exclude']) {
            $collection->add('*');
        }

        foreach ($this->options['files'] as $file) {
            $collection->add($file);
        }

        foreach ($this->options['exclude'] as $file) {
            $collection->remove($file);
        }

        $files = $collection->toArray();

        $total = count($files);
        $i = 0;

        foreach ($files as $file) {
            $filename = sprintf('%s/%s', rtrim($this->cwd, DIRECTORY_SEPARATOR), $file);

            $filesystem->remove($filename);

            if ($this->io && !$this->io->isVerbose()) {
                $this->output(sprintf('Removing: <comment>%d%%</comment>', round(++$i * 100 / $total)));
            } else {
                $this->output(sprintf("Removed %s", $file));
            }

            // remove empty directories
            $this->removeEmptyDirectories($this->cwd, $file);
        }

        if ($this->io && !$this->io->isVerbose()) {
            $this->output(sprintf('Removing: <comment>%d%%</comment>', 100));
        }

        return new ExecuteResult(
            array(),
            $files
        );
    }

    /**
     * Removes empty directories
     *
     * @param $cwd
     * @param $file
     */
    protected function removeEmptyDirectories($cwd, $file)
    {
        $filesystem = new Filesystem();

        $filename = sprintf('%s/%s', rtrim($cwd, DIRECTORY_SEPARATOR), $file);

        while ('.' !== dirname($file)) {
            $file     = dirname($file);
            $filename = dirname($filename);

            $finder = new Finder();
            $countFiles = $finder
                ->files()
                ->in($filename)
                ->ignoreDotFiles(false)
                ->ignoreVCS(false)
                ->count();

            if ($countFiles === 0) {
                $filesystem->remove($filename);

                if ($this->io && $this->io->isVerbose()) {
                    $this->output(sprintf("Removed %s", $file));
                }
            } else {
                return;
            }
        }
    }
}
