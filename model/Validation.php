<?php

class Validation{
    //private $loginData;

    public static function validateLoginForm(){
        $args = array(
            'login' => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => '/^[0-9A-Za-ząęłńśćźżó_-]{2,25}$/']],
            'passwd' =>  ['filter' => FILTER_VALIDATE_REGEXP, "options" => ['regexp' => '/^[0-9A-Za-ząęłńśćźżó_-]{2,25}$/' ]]
        );
        return filter_input_array(INPUT_POST, $args);

    }
    public static function validateRegistrationForm(){
        $args = [
            'login' => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => '/^[0-9A-Za-ząęłńśćźżó_-]{2,25}$/']],
            'name' => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => '/^[A-Z]{1}[a-ząęłńśćźżó-]{2,25}$/']],
            'surname' => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => '/^[A-Z]{1}[a-ząęłńśćźżó-]{2,35}$/']],
            'email' => ['filter' => FILTER_VALIDATE_EMAIL],
            'passwd' =>  ['filter' => FILTER_VALIDATE_REGEXP, "options" => ['regexp' => '/^[0-9A-Za-ząęłńśćźżó_-]{2,25}$/' ]]

        ];
        return filter_input_array(INPUT_POST, $args);
    }
}