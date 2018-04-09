<?php 
namespace App\Validate;

use Respect\Validation\Validator as Respect;
use Respect\Validation\Exceptions\NestedValidationException;

class Validator
{
    public function valid($request, $rules)
    {
        $errorMsg = '';
        $errors = [];

        foreach ($rules as $field => $rule) {
            try {
                $rule->assert($request->getParam($field));
            } catch (NestedValidationException $e) {
                $errors[$field] = $e->findMessages(
                    $this->messages[$field]
                );
            }
            if (isset($errors[$field])){
                foreach ($errors[$field] as $key => $val) {
                    if (!empty($val)) {
                        $errorMsg = $val;
                        break;
                    }
                }
            }
            if (!empty($errorMsg)) {
                break;
            }
        }

        return $errorMsg;
    }
}
