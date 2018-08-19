<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Phalcon\Shared\Config;

use Phalcon\Config;
use Phalcon\Di\InjectionAwareInterface;
use TwistersFury\Phalcon\Shared\Traits\Injectable;

class Theme extends Config implements InjectionAwareInterface
{
    use Injectable;

    private $parentConfig = null;

    public function hasParent()
    {
        return $this->get('parent') !== null;
    }

    public function getParent() : ?Theme
    {
        if (!$this->hasParent()) {
            return null;
        } elseif ($this->parentConfig !== null) {
            return $this->parentConfig;
        }

        $this->parentConfig = new static(include APPLICATION_PATH . '/themes/config.php');
        $this->parentConfig->merge(new static(
            include APPLICATION_PATH . '/themes/' . $this->get('parent') . '/config.php'
        ));

        return $this->parentConfig;
    }

    public function getStylesheets() : array
    {
        return $this->getFiles('stylesheets');
    }

    public function getScripts() : array
    {
        return $this->getFiles('javascript');
    }

    private function getFiles(string $fileType) : array
    {
        if (($fileCache = $this->getDi()->get('serviceCache')->get(
            'themeConfig-' . $this->get('name') . '-' . $fileType
        ))) {
            return $fileCache;
        }

        $fileCache = $this->get($fileType)->toArray();
        $config    = $this;

        while ($config->hasParent()) {
            $config    = $config->getParent();
            $fileCache = $config->get($fileType)->toArray() + $fileCache;
        }

        $this->getDi()->get('serviceCache')->save(
            'themeConfig-' . $this->get('name') . '-' . $fileType,
            $fileCache
        );

        return $fileCache;
    }
}
