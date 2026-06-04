<?php

namespace App\Service;

class Cache
{
    private $baseCacheDir = '/mnt/temporario/cache';
    private $cacheDir;
    private $cacheFile;
    private $useCache = true;

    public function __construct($dir, $file)
    {
        $this->cacheDir = $this->baseCacheDir . '/' . $dir;

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0775, true);
        }

        if (!is_writable($this->cacheDir)) {
            $this->useCache = false;
        }

        $this->cacheFile = $this->cacheDir . '/' . $file . '.json';
    }

    public function cacheFileExists()
    {
        return file_exists($this->cacheFile);
    }

    public function writeCache($content)
    {
        if (!$this->useCache) {
            return false;
        }

        return file_put_contents($this->cacheFile, $content);
    }

    public function getFromCache()
    {
        if (false === $this->useCache) {
            return '';
        }

        if ($this->cacheFileExists()) {
            return file_get_contents($this->cacheFile);
        }

        return '';
    }

    public function cleanCache()
    {
        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }

        return true;
    }

    public function getCacheFile() {
        return $this->cacheFile;
    }
}
