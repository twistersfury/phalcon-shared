<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Phalcon\Shared\Mvc\Controller;

use Phalcon\Config;
use Phalcon\Mvc\Controller;

/**
 * Class AbstractController
 */
abstract class AbstractController extends Controller
{
    public function initialize()
    {
        $this->assets->useImplicitOutput(false);

        $this->configureCollection($this->config->system->theme);

        $this->configureTitle()
            ->configureJavascript()
            ->configureStylesheets()
            ->configureCollections();
    }

    protected function getStylesheetFiles() : array
    {
        return $this->getDi()->get('themeConfig')->getStylesheets();
    }

    protected function getJavascriptFiles() : array
    {
        return $this->getDi()->get('themeConfig')->getScripts();
    }

    private function configureTitle() : self
    {
        $this->tag->setTitle(
            $this->config->system->title->default
        );

        $this->tag->setTitleSeparator(
            $this->config->system->title->separator
        );

        return $this;
    }

    private function configureCollections(): self
    {
        foreach($this->getDi()->get('themeConfig')->getCollections() as $collectionName => $collection) {
            $this->configureCollection($collectionName, $collection);
        }

        return $this;
    }

    private function configureCollection(string $collectionName, Config $collection = null): self
    {
        $name = $collection->name ?? $collectionName;

        $this->assets->collection($name . '-internal')
                     ->setSourcePath(APPLICATION_PATH . '/themes/' . $this->config->system->theme)
                     ->setLocal(true)
                     ->setPrefix('assets/' . $this->config->system->theme . '/');

        $this->assets->collection($name . '-external')
                     ->setLocal(false);

        if ($collection !== null) {
            $this->configureAssets($name, $this->getDi()->get('themeConfig')->getFiles($collectionName), $collection);
        }

        return $this;
    }

    private function configureAssets(string $collectionName, array $assets, Config $collection): self
    {
        $basePath = $collection->type;
        if ($collectionName !== 'default') {
            $basePath = $collectionName;
        }

        foreach($assets as $asset) {
            if (!preg_match('#https?://(.*?)#', $asset)) {
                $this->assets->collection($collectionName . '-internal')->{'add' . $collection['type']}($basePath . '/' . $asset);
            } else {
                $this->assets->collection($collectionName . '-external')->{'add' . $collection['type']}($asset);
            }
        }

        return $this;
    }

    private function configureJavascript() : self
    {
        return $this->configureAssets(
            $this->config->system->theme,
            $this->getJavascriptFiles(),
            new Config(['type' => 'js'])
        );
    }

    private function configureStylesheets() : self
    {
        return $this->configureAssets(
            $this->config->system->theme,
            $this->getStylesheetFiles(),
            new Config(['type' => 'css'])
        );
    }
}
