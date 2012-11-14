<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel {

    public $username;

    public function rules() {
        return array(
            // username and password are required
            array('username', 'required'),
        );
    }
    
    public function attributeLabels() {
        return array(
            'username'=>'Имя (ник)'
        );
    }

}
