<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 7/28/17
     * Time: 12:40 AM
     */

    namespace TwistersFury\Phalcon\Shared\Forms;

    use Phalcon\Forms\Form;

    abstract class AbstractCrudForm extends Form
    {
        public function initialize()
        {
            $this->setAction(
                $this->url->get(
                    [
                        'for' => $this->dispatcher->getModuleName() . '-' . $this->dispatcher->getControllerName() . '-save',
                        'entity' => $this->getEntity() ? $this->getEntity()->getId() : 0
                    ]
                )
            );
        }
    }