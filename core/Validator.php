<?php

declare(strict_types=1);

namespace Core;

class Validator
{
    private $errors = [];
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Apply a set of rules to the data.
     */
    public function validate(array $rules)
    {
        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {

                $ruleName = $rule;
                $param = null;
                
                // Check for rules with parameters like min:8
                if (strpos($rule, ':') !== false) {
                    // $ruleName = min | $param = 8
                    list($ruleName, $param) = explode(':', $rule, 2);
                }

                $methodName = 'validate' . ucfirst($ruleName);

                // Usually $data = $_POST[$value]
                $value = $this->data[$field] ?? null;

                if (method_exists($this, $methodName)) {
                    $this->$methodName($field, $value, $param);
                }
            }
        }
        return $this;
    }

    /**
     * Validation rules.
     */
    protected function validateRequired($field, $value)
    {
        if (empty($value)) {
            $this->addError($field, "The {$field} is required.");
        }
    }

    protected function validateEmail($field, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "Please provide a valid email address.");
        }
    }

    protected function validateMin($field, $value, $param)
    {
        if (strlen($value) < $param) {
            $this->addError($field, "The {$field} must be at least {$param} characters long.");
        }
    }

    protected function validateMax($field, $value, $param)
    {
        if (strlen($value) > $param) {
            $this->addError($field, "The {$field} cannot excede {$param} characters.");
        }
    }
    
    // TODO: Find a way to validate the provided password.
    // protected function validateVerify($field, $value, $param) {
    //     if(password_verify($value, ))
    // }

    /**
     * Add the error to the error array
     */
    public function addError($field, $message)
    {
        $this->errors[$field]['message'] = $message;
    }

    public function fails()
    {
        return !empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
