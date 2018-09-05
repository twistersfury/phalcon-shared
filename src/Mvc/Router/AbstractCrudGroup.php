<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

    namespace TwistersFury\Phalcon\Shared\Mvc\Router;

    use Phalcon\Mvc\Model;
    use Phalcon\Mvc\Model\CriteriaInterface;
    use Phalcon\Mvc\Router\Group;
    use Phalcon\Mvc\Router\Route;
    use Phalcon\Text;
    use ReflectionClass;
    use TwistersFury\Phalcon\Shared\Traits\Injectable;

    abstract class AbstractCrudGroup extends Group
    {
        use Injectable;

        abstract public function convertEntity(int $entityId) : ?Model;

        public function getModule(): string
        {
            return $this->prepareSegment(
                $this->explodeClass()[2]
            );
        }

        protected function explodeClass(): array
        {
            return explode('\\', get_called_class());
        }

        protected function buildCriteria(string $serviceName): CriteriaInterface
        {
            return $this->getDI()->get('criteriaFactory')->get($serviceName);
        }

        /**
         * @return string
         * @throws \ReflectionException
         */
        public function getController(): string
        {
            return $this->prepareSegment(
                str_replace(
                    'Group',
                    '',
                    (new ReflectionClass(get_called_class()))->getShortName()
                )
            );
        }

        public function getParentController() : ?string {
            return null;
        }

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
            $routePrefix = '/' . $this->getModule() . '/';

            if ($this->hasParent()) {
                if ($this->getParentController()) {
                    $routePrefix .= $this->getParentController() . '/';
                }

                $routePrefix .= '{parentEntity:\d+}/';
            }

            $routePrefix .= $this->getController();

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
                $this->addGet(
                    '',
                    [
                        'action' => 'index'
                    ]
                ),
                'index'
            );

            $this->processRoute(
                $this->add(
                    '/create',
                    [
                        'action' => 'create'
                    ]
                ),
                'create'
            )->processRoute(
                $this->add(
                    '/{entity:\d+}',
                    [
                        'action' => 'retrieve'
                    ]
                ),
                'retrieve'
            )->processRoute(
                $this->add(
                    '/{entity:\d+}/update',
                    [
                        'action' => 'update',
                    ]
                ),
                'update'
            )->processRoute(
                $this->add(
                    '/{entity:\d+}/delete',
                    [
                        'action' => 'delete'
                    ]
                ),
                'delete'
            )->processRoute(
                $this->addPost(
                    '/save',
                    [
                        'action' => 'save'
                    ]
                ),
                'save'
            )->processRoute(
                $this->add(
                    '/list',
                    [
                        'action' => 'list'
                    ]
                ),
                'list'
            );
        }

        /**
         * @param Route|\Phalcon\Mvc\Router\RouteInterface $route
         * @param string                       $routeType
         *
         * @return \TwistersFury\Phalcon\Shared\Router\AbstractCrudGroup
         */
        protected function processRoute(Route $route, string $routeType) : AbstractCrudGroup
        {
            $route->setName(
                $this->getModule() . '-' . $this->getController() . '-' . $routeType
            )->convert('entity', [$this, 'convertEntity']);

            if ($this->hasParent()) {
                $route->convert('parentEntity', [$this, 'convertParentEntity']);
            }

            return $this;
        }

        protected function prepareSegment(string $routeSegment): string
        {
            return str_replace('_', '-', Text::uncamelize($routeSegment));
        }
    }