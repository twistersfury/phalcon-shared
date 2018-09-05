<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Phalcon\Shared\Mvc\Controller;

use Phalcon\Mvc\Controller;

/**
 * Class AbstractController
 */
abstract class AbstractController extends Controller
{
    public function initialize()
    {
        $this->assets->useImplicitOutput(false);

        $this->assets->collection($this->config->system->theme . '-internal')
            ->setSourcePath(APPLICATION_PATH . '/themes/' . $this->config->system->theme)
            ->setLocal(true)
            ->setPrefix('assets/' . $this->config->system->theme . '/');

        $this->assets->collection($this->config->system->theme . '-external')
            ->setLocal(false);

        $this->configureTitle()
            ->configureJavascript()
            ->configureStylesheets();
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

    private function configureJavascript() : self
    {
        foreach ($this->getJavascriptFiles() as $javascriptFile) {
            if (!preg_match('#https?://(.*?)#', $javascriptFile)) {
                $this->assets->collection($this->config->system->theme . '-internal')->addJs('js/' . $javascriptFile);
            } else {
                $this->assets->collection($this->config->system->theme . '-external')->addJs($javascriptFile);
            }
        }

        return $this;
    }

    private function configureStylesheets() : self
    {
        foreach ($this->getStylesheetFiles() as $cssFile) {
            if (!preg_match('#https?://(.*?)#', $cssFile)) {
                $this->assets->collection($this->config->system->theme . '-internal')->addCss('css/' . $cssFile);
            } else {
                $this->assets->collection($this->config->system->theme . '-external')->addCss($cssFile);
            }
        }

        return $this;
    }
}
