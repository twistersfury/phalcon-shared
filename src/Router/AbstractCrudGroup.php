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

    abstract class AbstractCrudGroup extends Group
    {
        abstract public function getModule() : string;
        abstract public function getController() : string;
        abstract public function convertEntity(int $entityId) : ?Model;

        public function initialize() {
            $this->setPrefix('/' . $this->getModule() . '/' . $this->getController() . '/')
                 ->setPaths(
                     [
                         'module'     => $this->getModule(),
                         'controller' => $this->getController()
                     ]
                 );

            $this->add(
                'create',
                [
                    'action' => 'create'
                ]
            )->setName($this->getModule() . '-' . $this->getController() . '-create');

            $this->add(
                '{entity:\d+}',
                [
                    'action' => 'retrieve'
                ]
            )->setName($this->getModule() . '-' . $this->getController() . '-view')->convert('entity', [$this, 'convertEntity']);

            $this->add(
                '{entity:\d+}/update',
                [
                    'action' => 'update',
                ]
            )->setName($this->getModule() . '-' . $this->getController() . '-update')->convert('entity', [$this, 'convertEntity']);

            $this->add(
                '{entity:\d+}/delete',
                [
                    'action' => 'delete'
                ]
            )->setName($this->getModule() . '-' . $this->getController() . '-delete')->convert('entity', [$this, 'convertEntity']);

            $this->addPost(
                'save',
                [
                    'action' => 'save'
                ]
            )->setName($this->getModule() . '-' . $this->getController() . '-save')->convert('entity', [$this, 'convertEntity']);

            $this->add(
                'list',
                [
                    'action' => 'list'
                ]
            )->setName($this->getModule() . '-' . $this->getController() . '-list');
        }
    }