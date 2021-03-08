<?php
namespace eBayAPI\exception;

class RequestException extends \Exception {
    private $errors;

    public function getErrors() {
        return $this->errors;
    }

    public function __construct($errors, $message, $code) {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }
}