<?php
namespace eBayAPI\exception;

class RequestException extends \Exception {
    private $errors;

    public function getErrors() {
        return $this->errors;
    }

    public function __construct($errors) {
        $this->errors = $errors;
    }
}