<?php
/**
 * Created by PhpStorm.
 * User: fenikkusu
 * Date: 2018-09-08
 * Time: 22:40
 */

namespace TwistersFury\Phalcon\Shared\Validation\Validator;

use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Validation\Validator;
use Phalcon\Validation;
use TwistersFury\Phalcon\Shared\Traits\Injectable;

class RecordExists extends Validator implements InjectionAwareInterface
{
    use Injectable;

    public function __construct(array $options = null) {
        if ($options === null) {
            $options = [];
        }

        $options = array_merge(
            [
                'field'   => 'id',
                'service' => false,
                'message' => 'The requested record does not exist.'
            ],
            $options
        );

        parent::__construct($options);
    }

    public function validate(Validation $validation, $attribute)
    {
        $fieldValue = $validation->getValue($attribute);

        if (!$this->buildCriteria($fieldValue)->execute()->getFirst()) {
            $validation->appendMessage(
                $this->getDI()->get(
                    Validation\Message::class,
                    [
                        $this->getOption('message', $attribute, 'RecordExists')
                    ]
                )
            );

            return false;
        }

        return true;
    }

    private function buildCriteria($fieldValue)
    {
        if (!$this->getOption('service')) {
            throw new \RuntimeException('Service Not Specified');
        } else if (!$this->getOption('field')) {
            throw new \RuntimeException('Field Not Specified');
        }

        return $this->getDI()->get('criteriaFactory')->get($this->getOption('service'))
            ->andWhere(
                $this->getOption('field') . ' = :fieldValue:',
                [
                    'fieldValue' => $fieldValue
                ]
            );
    }
}