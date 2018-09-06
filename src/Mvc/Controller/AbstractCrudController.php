<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

    namespace TwistersFury\Phalcon\Shared\Mvc\Controller;

    use Phalcon\Forms\Form;
    use Phalcon\Mvc\Controller;
    use Phalcon\Mvc\Model\CriteriaInterface;
    use TwistersFury\Phalcon\Shared\Interfaces\EntityInterface;

    abstract class AbstractCrudController extends AbstractController
    {
        protected $wasLoaded = false;
        private $currentForm = null;

        public function initialize()
        {
            parent::initialize();

            //Adding Current Entity To All Actions (Will Be Null In Cases Where Not Specified)
            $this->view->setVars(
                [
                    'entity' => $this->getEntity(),
                    'form'   => $this->getForm()
                ]
            );

            $this->wasLoaded = true;
        }

        protected function getEntityClass() : string {
            return str_replace(['Controllers', 'Controller'], ['Models', ''], get_called_class());
        }

        /**
         * @return EntityInterface|\Phalcon\Mvc\Model|null
         */
        protected function getEntity() : ?EntityInterface
        {
            return $this->dispatcher->getParam('entity') ?: null;
        }

        /**
         * @return EntityInterface|\Phalcon\Mvc\Model
         */
        protected function createEntity() : EntityInterface {
            return $this->di->get($this->getEntityClass());
        }

        protected function getFormClass()
        {
            $entityClass = str_replace('Mvc\Controller', 'Forms', get_called_class());

            return preg_replace(
                '#Controller$#',
                '',
                $entityClass
            );
        }

        protected function getForm() : Form
        {
            if ($this->currentForm === null) {
                $this->currentForm = $this->di->get($this->getFormClass(), [$this->getEntity() ?: $this->createEntity()]);
            }

            return $this->currentForm;
        }

        public function createAction()
        {
            $this->dispatcher->setParam('entity', $this->createEntity());
            $this->dispatcher->forward(
                [
                    'action' => 'update'
                ]
            );
        }

        public function retrieveAction()
        {
            $this->tag->prependTitle($this->getEntity()->getTitle()  ?: 'View Record');
        }

        public function updateAction()
        {
            $this->tag->prependTitle('Edit Record');
        }

        public function deleteAction()
        {
            $this->getEntity()->delete();
            $this->flashSession->success('The record has been removed.');
        }

        public function saveAction()
        {
            if ($this->getForm()->isValid($this->request->getPost())) {
                $entityModel = $this->getEntity() ?: $this->createEntity();
                if ($entityModel->save($this->prepareEntityData($this->request->getPost()))) {
                    $this->flashSession->success('Record Updated Successfully');
                    return $this->response->redirect(
                        [
                            'for' => $this->dispatcher->getModuleName() . '-' . $this->getName() . '-list'
                        ]
                    );
                }

                foreach($entityModel->getMessages() as $message) {
                    $this->flashSession->error($message);
                }
            }

            foreach($this->getForm()->getMessages() as $message) {
                $this->flashSession->error($message);
            }

            return $this->response->redirect(
                [
                    'for' => $this->dispatcher->getModuleName() . '-' . $this->getName() . '-update',
                    'entity' => $this->getEntity() ? $this->getEntity()->getId() : 0
                ]
            );
        }

        protected function prepareEntityData(array $entityData): array
        {
            return $entityData;
        }

        public function listAction()
        {
            $this->view->setVar('records', $this->buildCriteria()->execute());
        }

        public function indexAction()
        {
            return $this->dispatcher->forward(
                [
                    'action' => 'list'
                ]
            );
        }

        public function getName() : string
        {
            return $this->dispatcher->getControllerName();
        }

        abstract protected function buildCriteria() : CriteriaInterface;
    }