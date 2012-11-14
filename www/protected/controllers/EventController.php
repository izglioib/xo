<?php

class EventController extends Controller {

    public function actionIndex() {
		
        echo(Yii::app()->events->waitForEvent('MyEvent',5000,$message));
        
        echo ($message);
 
    }
    
    
    public function actionEmit() {
        
        echo(Yii::app()->events->emitEvent('MyEvent','MyMessage'));
 
    }
    
    

}

