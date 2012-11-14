<?php
/*
 * Yii Event component
 * provides cross thread event functionality
 */


class YiiEvents extends CApplicationComponent{
    
    public $defaultTimeout = 30000;
    public $nodeHost = '127.0.0.1:8000';
    
    public function waitForEvent($name,$timeout=null,&$message = null){
        if(empty($timeout))
            $timeout = $this->defaultTimeout;
        
        set_time_limit( ($timeout/1000) + 5 );
        
        $res = json_decode(file_get_contents('http://'.$this->nodeHost.'/?action=waitForEvent&name='.$name.'&timeout='.$timeout),true);
        
        $message = $res['message'];
		$res['status'] = false;
        
        return $res['status'];
        
    }
    
    public function emitEvent($name,$message = null) {
        if(empty($timeout))
            $timeout = $this->defaultTimeout;
        
        set_time_limit( ($timeout/1000) + 5 );
        file_get_contents('http://'.$this->nodeHost.'/?action=emitEvent&name='.$name.'&message='.$message);
    }    
    
}