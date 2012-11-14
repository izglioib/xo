<?php

class SiteController extends Controller {

	private function _getName() {
		$name = Yii::app()->session->get('userName');
		if (empty($name)) {
			$this->render('getName', array('model' => new LoginForm));
			Yii::app()->end();
		}
		return $name;
	}

	public function actionSetName() {
		$model = new LoginForm;
		if (!empty($_POST['LoginForm'])) {
			$model->attributes = $_POST['LoginForm'];
			if ($model->validate()) {
				Yii::app()->session->add('userName', $model->username);
				Yii::app()->session->add('games', 0);
				Yii::app()->session->add('wins', 0);
				$this->redirect('/');
			}
		}

		$this->render('getName', array('model' => $model));
	}

	private function _getGame($name) {

		// if game in session
		$game = Yii::app()->session->get('game');
		if (!empty($game)) {
			$gameModel = Game::model()->findByPk($game['id']);
			if (!empty($gameModel))
				return $gameModel;
		}

		// find waiting games
		$criteria = new CDbCriteria();
		$criteria->compare('status', 0);
		$criteria->compare('user_o', '<>' . session_id());
		$criteria->compare('user_x', '<>' . session_id());
		$gameModel = Game::model()->find($criteria);
		if (!empty($gameModel)) {
			if (empty($gameModel->user_x)) {
				$xo = 'x';
			} elseif (empty($gameModel->user_o)) {
				$xo = 'o';
			} else {
				throw new CHttpException(500, 'Game status 0 while no free user slot');
			}
			$gameModel->status = 1;
			$attr = 'user_' . $xo;
			$gameModel->$attr = session_id();
			$attr = 'user_' . $xo . '_name';
			$gameModel->$attr = $name;

			$attr = 'user_' . $xo . '_games';
			$gameModel->$attr = Yii::app()->session->get('games', 0);
			$attr = 'user_' . $xo . '_wins';
			$gameModel->$attr = Yii::app()->session->get('wins', 0);

			$gameModel->xo = $xo;

			Yii::app()->session->add('game', array('id' => $gameModel->id, 'xo' => $xo));

			if ($gameModel->save()) {
				Yii::app()->events->emitEvent('start' . $gameModel->id);
				return $gameModel;
			} else {
				throw new CHttpException(500, 'Can\'t save game status');
			}
		}

		// create new game
		$gameModel = new Game;
		$xo = (rand(0, 10) > 5) ? 'x' : 'o';
		$attr = 'user_' . $xo;
		$gameModel->$attr = session_id();
		$attr = 'user_' . $xo . '_name';
		$gameModel->$attr = $name;

		$attr = 'user_' . $xo . '_games';
		$gameModel->$attr = Yii::app()->session->get('games', 0);
		$attr = 'user_' . $xo . '_wins';
		$gameModel->$attr = Yii::app()->session->get('wins', 0);

		$gameModel->xo = $xo;


		if ($gameModel->save()) {
			Yii::app()->session->add('game', array('id' => $gameModel->id, 'xo' => $xo));
			return $gameModel;
		} else {
			throw new CHttpException(500, 'Can\'t save game status');
		}
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
        public function actionIndex() {

		$name = $this->_getName();

		$game = $this->_getGame($name);

		if ($game->status == 0) {
			$this->render('waitForPlayer', array('game' => $game, 'name' => $name));
		} else {
			$this->render('game', array('game' => $game, 'name' => $name));
		}
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError() {
		if ($error = Yii::app()->errorHandler->error) {
			if (Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	public function actionWaitForPlayerEvent() {
		$name = $this->_getName();

		$game = $this->_getGame($name);


		if ($game->status > 0) {
			// if we missed event somehow
			$status = 1;
		} else {
			// wait for event
			$status = Yii::app()->events->waitForEvent('start' . $game->id, Yii::app()->params['longPoolTimeoutBackend']) ? 1 : 0;
		}


		echo(json_encode(array(
			'status' => $status
		)));

		Yii::app()->end();
	}

	public function actionWaitForMoveEvent() {
		$name = $this->_getName();

		$game = $this->_getGame($name);


		if ($game->active) {
			// if we missed event somehow
			$status = 1;
		} else {
			// wait for event
			$status = Yii::app()->events->waitForEvent('move' . $game->id, Yii::app()->params['longPoolTimeoutBackend']) ? 1 : 0;
		}
		$status = 1;

		echo(json_encode(array(
			'status' => $status
		)));

		Yii::app()->end();
	}

	public function actionRefreshGameBoard() {

		$name = $this->_getName();

		$game = $this->_getGame($name);

		$this->renderPartial('_gameboard', array('game' => $game));
	}

	public function actionSetMove($cell) {
		$cells = array(11, 12, 13, 21, 22, 23, 31, 32, 33);

		if (!in_array($cell, $cells))
			throw new CHttpException(500, 'Incorect input');

		$name = $this->_getName();

		$game = $this->_getGame($name);

		$attr_name = 'cell' . $cell;

		if (empty($game->$attr_name)) {
			$game->$attr_name = $game->move;
			$game->move = 3 - $game->move;
			$game->checkEnd();
			$game->save();

			Yii::app()->events->emitEvent('move' . $game->id);
		}
	}

	public function actionNewGame() {
		$name = $this->_getName();

		$game = $this->_getGame($name);

		if ($game->status >= 2) {
			$game_session = Yii::app()->session->get('game');
			Yii::app()->session->add('games', Yii::app()->session->get('games', 0) + 1);

			if (($game->status == 3) && ($game_session['xo'] == 'x')) {
				Yii::app()->session->add('wins', Yii::app()->session->get('wins', 0) + 1);
			}

			if (($game->status == 4) && ($game_session['xo'] == 'o')) {
				Yii::app()->session->add('wins', Yii::app()->session->get('wins', 0) + 1);
			}

			Yii::app()->session->remove('game');
		}
		
		$this->redirect('/');
	}
	
	public function actionLeaveGame() {

		Yii::app()->session->remove('game');
		
		$this->redirect('/');
	}
	

}