<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 7/28/17
     * Time: 12:42 AM
     */

    namespace TwistersFury\Phalcon\Shared\Mvc;

    use Phalcon\Forms\Form;
    use Phalcon\Mvc\Controller;
    use TwistersFury\Phalcon\Shared\Interfaces\EntityInterface;

    abstract class AbstractCrudController extends Controller
    {
        protected $wasLoaded = false;
        private $currentForm = null;
        private $controllerName = null;

        public function initialize()
        {
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
            return str_replace(['Controllers', 'Controller'], ['Forms', ''], get_called_class());
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
        }

        public function saveAction()
        {
            if ($this->getForm()->isValid($this->request->getPost())) {
                $entityModel = $this->getEntity() ?: $this->createEntity();
                if ($entityModel->save($this->request->getPost())) {
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

        public function getName() : string
        {
            if ($this->controllerName === null) {
                $this->controllerName = array_reverse(explode('\\', get_called_class()))[0];
            }

            return $this->controllerName;
        }
    }