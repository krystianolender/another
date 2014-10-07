<?php

interface InterfaceValidate {
    public function getRule();
    public function setRule($rule);
    public function valid($value, $modelName, $inputName);
    public function getErrorMessage();
}