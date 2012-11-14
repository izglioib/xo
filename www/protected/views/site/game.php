<script type="text/javascript">
	function refreshGameboard(){
		$('#cntGameboard').load('<?= CHtml::normalizeUrl(array('refreshGameBoard')) ?>');	
	}
	
	function poll(){
		$.ajax({ url: "<?= CHtml::normalizeUrl(array('waitForMoveEvent')) ?>", success: function(data){
				if(data.status == 1) {
					refreshGameboard();
				}else{
					poll();
				}
			}, dataType: "json", timeout: <?= Yii::app()->params['longPoolTimeout'] ?>, error: poll, async: true});
	} 	
	

	function setInterface(active){
		if(active){
			$('#cntSysmessage').text('Your move...');
		}else{
			$('#cntSysmessage').text('Waiting for other player.');
			poll();
		}
	}

</script>


<div style="width: 900px;">
    <div style="width: 100px; float: left;" >
        <h1><?= $game->user_x_name ?></h1>
        <p>Plays: X</p>
        <p>Wins: <?=$game->user_x_wins?>/<?=$game->user_x_games?></p>
    </div>
    <div style="width: 100px; float: right;">
        <h1><?= $game->user_o_name ?></h1>
        <p>Plays: O</p>
        <p>Wins: <?=$game->user_o_wins?>/<?=$game->user_o_games?></p>
    </div>
    <div id="cntGameboard" style="width: 700px; text-align: center; padding-top: 20px;">
		<?php $this->renderPartial('_gameboard', array('game' => $game)); ?>
    </div>
</div>
<div style="clear: both"></div>
<p id="cntSysmessage"></p>

<?= CHtml::link('Leave Game', array('leaveGame')) ?>
