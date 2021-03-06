<?php
/**
 * Created by PhpStorm.
 * User: fenikkusu
 * Date: 2018-09-07
 * Time: 14:01
 */

namespace TwistersFury\Phalcon\Shared\Mvc\Router\Group;

use Phalcon\Mvc\Router\Group;
use Phalcon\Mvc\Router\RouteInterface;
use Phalcon\Text;

class AbstractGroup extends Group
{
    /**
     * @throws \ReflectionException
     */
    public function initialize()
    {
        $this->setPrefix($this->buildPrefix())
             ->setPaths(
                 [
                     'module'     => $this->getModule(),
                     'controller' => $this->getController()
                 ]
             );
    }

    protected function hasParent() : bool
    {
        return false;
    }

    protected function hasEntity(): bool
    {
        return false;
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
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


    protected function explodeClass(): array
    {
        return explode('\\', get_called_class());
    }

    protected function getModule(): string
    {
        return $this->prepareSegment(
            $this->explodeClass()[2]
        );
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    protected function getController(): string
    {
        return $this->prepareSegment(
            str_replace(
                'Group',
                '',
                (new \ReflectionClass(get_called_class()))->getShortName()
            )
        );
    }

    protected function getParentController() : ?string {
        return null;
    }

    protected function prepareSegment(string $routeSegment): string
    {
        return str_replace('_', '-', Text::uncamelize($routeSegment));
    }

    public function convertEntity($entityId)
    {
        return $entityId;
    }

    public function convertParentEntity($parentEntity)
    {
        return $parentEntity;
    }

    protected function _addRoute($pattern, $paths = null, $httpMethods = null): RouteInterface
    {
        /** @var \Phalcon\Mvc\Router\Route $route */
        $route = parent::_addRoute($pattern, $paths, $httpMethods);

        if ($this->hasEntity()) {
            $route->convert(
                'entity',
                [
                    $this,
                    'convertEntity'
                ]
            );
        }

        if ($this->hasParent()) {
            $route->convert(
                'parentEntity',
                [
                    $this,
                    'convertParentEntity'
                ]
            );
        }

        return $route;
    }
}