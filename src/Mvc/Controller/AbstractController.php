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

        $this->configureCollection($this->config->system->theme)
            ->configureCollection($this->config->system->theme . '-module');

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
                $this->assets->collection($collectionName . '-internal')->{'add' . $collection['type']}(
                    preg_replace(
                        '#\/+#',
                        '/',
                        $basePath . '/' . $asset
                    )
                );
            } else {
                $this->assets->collection($collectionName . '-external')->{'add' . $collection['type']}($asset);
            }
        }

        return $this;
    }

    private function configureJavascript() : self
    {
        return $this->addJavascript(
            $this->themeConfig->getFiles('modules-js'),
            $this->config->system->theme . '-module'
        )->addJavascript(
            $this->getJavascriptFiles()
        );
    }

    private function configureStylesheets() : self
    {
        return $this->addCss(
            $this->themeConfig->getFiles('modules-css'),
            $this->config->system->theme . '-module'
        )->addCss(
            $this->getStylesheetFiles()
        );
    }

    protected function addJavascript(array $javascriptFiles, string $collectionName = null): self
    {
        if ($collectionName === null) {
            $collectionName = $this->config->system->theme;
        }

        return $this->configureAssets(
            $collectionName,
            $javascriptFiles,
            new Config(['type' => 'js'])
        );
    }

    protected function addCss(array $cssFiles, string $collectionName = null): self
    {
        if ($collectionName === null) {
            $collectionName = $this->config->system->theme;
        }

        return $this->configureAssets(
            $collectionName,
            $cssFiles,
            new Config(['type' => 'css'])
        );
    }
}
