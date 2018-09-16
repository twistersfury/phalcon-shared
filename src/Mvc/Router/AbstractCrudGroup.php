<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

    namespace TwistersFury\Phalcon\Shared\Mvc\Router;

    use Phalcon\Mvc\Model;
    use Phalcon\Mvc\Model\CriteriaInterface;
    use Phalcon\Mvc\Router\Route;
    use TwistersFury\Phalcon\Shared\Mvc\Router\Group\AbstractGroup;
    use TwistersFury\Phalcon\Shared\Traits\Injectable;
    use TwistersFury\Phalcon\Shared\Exceptions\RecordNotFound;

    abstract class AbstractCrudGroup extends AbstractGroup
    {
        use Injectable;

        abstract protected function getDefaultEntityType(): string;

        protected function getDefaultEntityParams(): array
        {
            return [];
        }

        protected function getDefaultParentType(): string
        {
            return '';
        }

        protected function getDefaultParentParams(): array
        {
            return [];
        }

        public function convertEntity($entityId, string $entityType = null, array $entityParams = null): ?Model
        {
            if ($entityId === 0 || $entityId === '0') {
                return null;
            }

            if ($entityType === null) {
                $entityType = $this->getDefaultEntityType();
            }

            if ($entityParams === null) {
                $entityParams = $this->getDefaultEntityParams();
            }

            /** @var Model $entity */
            $entity = $this->buildCriteria($entityType, $entityParams)->andWhere(
                'id = :entityId:',
                [
                    'entityId' => $entityId
                ]
            )->execute()->getFirst();

            if (!$entity) {
                throw new RecordNotFound();
            }

            return $entity;
        }

        public function convertParentEntity($parentEntity): ?Model
        {
            return $this->convertEntity(
                $parentEntity,
                $this->getDefaultParentType(),
                $this->getDefaultParentParams()
            );
        }

        protected function buildCriteria(string $serviceName, array $serviceParams = null): CriteriaInterface
        {
            return $this->getDI()->get('criteriaFactory')->get($serviceName, $serviceParams);
        }

        public function initialize()
        {
            parent::initialize();

            $this->processRoute(
                $this->addGet(
                    '',
                    [
                        'action' => 'index'
                    ]
                ),
                'index'
            )->processRoute(
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
                    '/{entity:\d+}/save',
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
         * @throws \ReflectionException
         */
        protected function processRoute(Route $route, string $routeType) : AbstractCrudGroup
        {
            $route->setName(
                $this->getModule() . '-' . $this->getController() . '-' . $routeType
            );

            return $this;
        }

        protected function hasEntity(): bool
        {
            return true;
        }
    }