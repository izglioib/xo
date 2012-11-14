<?php

/**
 * This is the model class for table "game".
 *
 * The followings are the available columns in table 'game':
 * @property string $id
 * @property integer $cell11
 * @property integer $cell12
 * @property integer $cell13
 * @property integer $cell21
 * @property integer $cell22
 * @property integer $cell23
 * @property integer $cell31
 * @property integer $cell32
 * @property integer $cell33
 * @property string $user_x
 * @property string $user_o
 * @property integer $status ( 0 - waiting, 1- playing, 2 - draw, 3- x win, 4 - o win)
 * @property string $user_x_name
 * @property string $user_o_name
 * @property integer $move
 * @property integer $user_x_games
 * @property integer $user_x_wins
 * @property integer $user_o_games
 * @property integer $user_o_wins
 */
class Game extends CActiveRecord {

    public $xo;
    
    public function afterFind() {
        parent::afterFind();
        $game = Yii::app()->session->get('game');
        if(!empty($game))
            $this->xo = $game['xo'];
    }
	
	public function getMoveId(){
		if($this->move == 1)
			return 'x';
		else 
			return 'o';
	}
	
	public function getActive(){
		return $this->xo == $this->moveId;
	}
	
	public function checkEnd(){
		$winpatterns = array(
			//horisontal
			array(11,12,13),
			array(21,22,23),
			array(31,32,33),
			// vertical
			array(11,21,31),
			array(12,22,32),
			array(13,23,33),
			// diagonal
			array(11,22,33),
			array(11,22,33),
		);
		
		foreach($winpatterns as $key => $pattern){
			$attr1='cell'.$pattern[0];
			$attr2='cell'.$pattern[1];
			$attr3='cell'.$pattern[2];
			if (!empty($this->$attr1) && ($this->$attr1 == $this->$attr2) && ($this->$attr2 == $this->$attr3)){
				// we have a winer
				if ($this->$attr1 == 1){ // x wins
					$this->status = 3;
				}else{ // o wins
					$this->status = 4;
				}
				return true;
			}
		}
		// if there is a free cell gamr not over
		foreach(array(11,12,13,21,22,23,31,32,33) as $k){
			$attr = 'cell'.$k;
			if(empty($this->$attr))
				return false;
		}
		
		// game over. draw.
		$this->status = 2;
		return true;
		
	}
	
	public function getWinnerName(){
		if($this->status == 3)
			return $this->user_x_name;
		if($this->status == 4)
			return $this->user_o_name;
		return '';
	}


	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Game the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'game';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
//			array('cell11, cell12, cell13, cell21, cell22, cell23, cell31, cell32, cell33', 'required'),
			array('cell11, cell12, cell13, cell21, cell22, cell23, cell31, cell32, cell33, status, move', 'numerical', 'integerOnly' => true),
			array('user_x, user_o', 'length', 'max' => 32),
			array('user_x_name, user_o_name', 'length', 'max' => 255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, cell11, cell12, cell13, cell21, cell22, cell23, cell31, cell32, cell33, user_x, user_o, status, user_x_name, user_o_name, move', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'cell11' => 'Cell11',
			'cell12' => 'Cell12',
			'cell13' => 'Cell13',
			'cell21' => 'Cell21',
			'cell22' => 'Cell22',
			'cell23' => 'Cell23',
			'cell31' => 'Cell31',
			'cell32' => 'Cell32',
			'cell33' => 'Cell33',
			'user_x' => 'User X',
			'user_o' => 'User O',
			'status' => 'Status',
			'user_x_name' => 'User X Name',
			'user_o_name' => 'User O Name',
			'move' => 'Move',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('cell11', $this->cell11);
		$criteria->compare('cell12', $this->cell12);
		$criteria->compare('cell13', $this->cell13);
		$criteria->compare('cell21', $this->cell21);
		$criteria->compare('cell22', $this->cell22);
		$criteria->compare('cell23', $this->cell23);
		$criteria->compare('cell31', $this->cell31);
		$criteria->compare('cell32', $this->cell32);
		$criteria->compare('cell33', $this->cell33);
		$criteria->compare('user_x', $this->user_x, true);
		$criteria->compare('user_o', $this->user_o, true);
		$criteria->compare('status', $this->status);
		$criteria->compare('user_x_name', $this->user_x_name, true);
		$criteria->compare('user_o_name', $this->user_o_name, true);
		$criteria->compare('move', $this->move);

		return new CActiveDataProvider($this, array(
					'criteria' => $criteria,
				));
	}
	
	

}