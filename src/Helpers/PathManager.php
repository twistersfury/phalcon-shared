<?php
    /**
     * PHP7 Phalcon Core Library
     *
     * @author Phoenix <phoenix@twistersfury.com>
     * @license http://www.opensource.org/licenses/mit-license.html MIT License
     * @copyright 2016 Twister's Fury
     */

    namespace TwistersFury\Phalcon\Shared\Helpers;
    
    use TwistersFury\Phalcon\Shared\Interfaces\PathManagerInterface;
    use TwistersFury\Phalcon\Shared\Traits\Injectable;

    class PathManager implements PathManagerInterface {
        use Injectable;

        protected $configData = null;

        public function __construct($useDefaults = true)
        {
            if ($useDefaults) {
                $this->setConfiguration();
            }
        }

        public function setConfiguration($configData = null)
        {
            if ($configData === null) {
                $configData = $this->buildDefaults();
            }

            foreach($configData as $keyName => $folderPath) {
                if (!file_exists($folderPath)) {
                    throw new \RuntimeException('Path ' . $folderPath . ' for ' . $keyName . ' does not exist');
                }
            }

            $this->configData = $configData;

            return $this;
        }

        protected function buildDefaults()
        {
            return [
                'root'    => TF_APP_ROOT,
                'config'  => TF_APP_ROOT . DIRECTORY_SEPARATOR . 'etc',
                'modules' => TF_APP_ROOT . DIRECTORY_SEPARATOR . 'modules',
                'cache'   => TF_APP_ROOT . DIRECTORY_SEPARATOR . 'cache',
            ];
        }

        public function getApplicationDir() : string {
            return $this->configData['root'];
        }

        public function getConfigDir() : string {
            return $this->configData['config'];
        }

        public function getModulesDir() : string {
            return $this->configData['modules'];
        }

        public function getCacheDir() : string {
            return $this->configData['cache'];
        }
    }