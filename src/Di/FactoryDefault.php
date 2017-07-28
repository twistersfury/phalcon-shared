<?php
	/**
	 * Created by PhpStorm.
	 * User: fenikkusu
	 * Date: 3/13/17
	 * Time: 9:11 PM
	 */

	namespace TwistersFury\Phalcon\Shared\Di;

    use Phalcon\Config;
    use Phalcon\Config\Adapter\Grouped;
    use Phalcon\Crypt;
    use Phalcon\Db\Adapter\Pdo\Mysql;
    use Phalcon\Flash\Session as FlashSession;
    use Phalcon\Mvc\Url;
    use Phalcon\Mvc\View;
    use Phalcon\Mvc\View\Engine\Volt;
    use Phalcon\Session\Adapter\Files;
    use TwistersFury\Phalcon\Shared\Helpers\PathManager;

    /**
     * Extension of Default Phalcon DIC. Class adds a few extra features to make the DIC easier to configure.
     *
     * @method Config getConfig
     * @method PathManager getPathManager;
     */
	class FactoryDefault extends AbstractFactory {
	    protected $priorityServices = [
            'registerPathManager', //Path Manager Is Needed For Config
            'registerConfig'       //Config Is Needed For Everything
        ];

        /**
         * Register Path Manager Helper Class
         */
        protected function registerPathManager() : FactoryDefault {
            if (!$this->has('pathManager')) {
                $this->setShared( 'pathManager', function() {
                    return $this->get( PathManager::class );
                } );
            }

            return $this;
        }

        protected function registerSession() : FactoryDefault {
            if (!$this->has('session')) {
                $this->setShared(
                    'session',
                    function() {
                        $sessionAdapter = $this->get(Files::class);
                        $sessionAdapter->start();

                        return $sessionAdapter;
                    }
                );
            }

            return $this;
        }

        /**
         * Register Default URL Instance (Defaults URL To Current Http Host Domain)
         */
		protected function registerUrl() : FactoryDefault {
			$this->setShared('url', function() {
				return $this->get(Url::class)
					->setBaseUri('//' . $this->get('request')->getHttpHost() . '/');
			});

			return $this;
		}

        /**
         * Register Default Config (With Dist)
         */
		protected function registerConfig() : FactoryDefault {
			$this->setShared('config', function() {
			    $configFiles = [
                    $this->get('pathManager')->getConfigDir() . '/config.dist.php'
                ];

			    if (file_exists($this->get('pathManager')->getConfigDir() . '/config.php')) {
                    $configFiles[] = $this->get('pathManager')->getConfigDir() . '/config.php';
                }

				return $this->get(Grouped::class, [$configFiles]);
			});

			return $this;
		}

        /**
         * Registers Databases From Configuration
         */
		protected function registerDatabases() : FactoryDefault {
            /**
             * @var  string $configName
             * @var  Config $dbConfig
             */
            foreach($this->get('config')->get('databases', []) as $configName => $dbConfig) {
                $this->set($configName, function() use ($dbConfig) {
                    $adapterClass = $dbConfig->get('adapter') ?: Mysql::class;

                    return $this->get(
                        $adapterClass,
                        [
                            $dbConfig->toArray()
                        ]
                    );
                });
            }

			return $this;
		}

        /**
         * Registers/Configures Volt Engine
         */
		protected function registerVoltEngine() : FactoryDefault {
			$this->set('voltEngine', function(View $view, FactoryDefault $di) {
			    /** @var Volt $volt */
				$volt = $di->get(
				    Volt::class,
					[
                        $view, $di
                    ]
				);

				$volt->setOptions(
					[
						'includePhpFunctions' => true,
					    'compiledPath'        => $this->get('pathManager')->getCacheDir() . DIRECTORY_SEPARATOR . 'volt',
					    'compileAlways'       => TF_DEBUG_MODE != TF_DEBUG_MODE_DISABLED,
					    'compiledSeparator'   => '-',
					    'compiledExtension'   => '.phtml'
					]
				);

				return $volt;
			});

			return $this;
		}

        /**
         * Registers Flash Session (Preset For Bootstrap 3)
         *
         * @return \TwistersFury\Phalcon\Shared\Di\FactoryDefault
         */
		protected function registerFlashSession() : FactoryDefault {
		    $this->set(
		        'flashSession',
                function() {
		            return $this->get(
		                FlashSession::class,
                        [
                            [
                                "error"   => "alert alert-danger",
                                "success" => "alert alert-success",
                                "notice"  => "alert alert-info",
                                "warning" => "alert alert-warning",
                            ]
                        ]
                    );
                }
            );

		    return $this;
        }

        /**
         * Registers Crypt (Using Key File)
         */
        protected function registerCrypt() {
		    $this->set('crypt', function() {
		        if (!$this->get('config')->get('keyFile')) {
		            throw new \LogicException('System Key Not Set');
                } else if (!file_exists($this->get('config')->get('keyFile'))) {
		            throw new \LogicException('System Key Missing');
                }

		        /** @var Crypt $phalconCrypt */
		        $phalconCrypt = $this->get(Crypt::class);

                $phalconCrypt->setKey(base64_decode(file_get_contents($this->getConfig()->get('keyFile'))));

                return $phalconCrypt;
            });

		    return $this;
        }
	}