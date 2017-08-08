<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 7/28/17
     * Time: 12:34 AM
     */

    namespace TwistersFury\Phalcon\Shared\Router;

    use Phalcon\Mvc\Model;
    use Phalcon\Mvc\Router\Group;
    use Phalcon\Mvc\RouterInterface;

    abstract class AbstractCrudGroup extends Group
    {
        abstract public function getModule() : string;
        abstract public function getController() : string;
        abstract public function convertEntity(int $entityId) : ?Model;

        public function convertParentEntity(int $parentId) : ?Model
        {
            return null;
        }

        public function hasParent() : bool
        {
            return false;
        }

        protected function buildPrefix() : string
        {
            $routePrefix = '/' . $this->getModule() . '/' . $this->getController() . '/';
            if ($this->hasParent()) {
                $routePrefix .= '{parentEntity:\d+}/';
            }

            return $routePrefix;
        }

        public function initialize()
        {
            $this->setPrefix($this->buildPrefix())
                 ->setPaths(
                     [
                         'module'     => $this->getModule(),
                         'controller' => $this->getController()
                     ]
                 );

            $this->processRoute(
                $this->add(
                    'create',
                    [
                        'action' => 'create'
                    ]
                ),
                'create'
            )->processRoute(
                $this->add(
                    '{entity:\d+}',
                    [
                        'action' => 'retrieve'
                    ]
                )
            )->processRoute(
                $this->add(
                    '{entity:\d+}/update',
                    [
                        'action' => 'update',
                    ]
                )
            )->processRoute(
                $this->add(
                    '{entity:\d+}/delete',
                    [
                        'action' => 'delete'
                    ]
                )
            )->processRoute(
                $this->addPost(
                    'save',
                    [
                        'action' => 'save'
                    ]
                )
            )->processRoute(
                $this->add(
                    'list',
                    [
                        'action' => 'list'
                    ]
                )
            );
        }

        /**
         * @param \Phalcon\Mvc\RouterInterface|\Phalcon\Mvc\Router\Route $route
         * @param string                       $routeType
         *
         * @return \TwistersFury\Phalcon\Shared\Router\AbstractCrudGroup
         */
        protected function processRoute(RouterInterface $route, string $routeType) : AbstractCrudGroup
        {
            $route->setName(
                $this->getModule() . '-' . $this->getController() . '-' . $routeType
            )->convert('entity', [$this, 'convertEntity']);

            if ($this->hasParent()) {
                $route->convert('parentEntity', [$this, 'convertParentEntity']);
            }

            return $this;
        }
    }