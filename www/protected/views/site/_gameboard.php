<?php $replace = array('' => '', '0' => '', '1' => 'X', '2' => '0'); ?>
<?php if ($game->status == 1): ?>
	<table class="gameboard">
		<tr>
			<td cell="11"><?= $replace[$game->cell11] ?></td>
			<td cell="12"><?= $replace[$game->cell12] ?></td>
			<td cell="13"><?= $replace[$game->cell13] ?></td>
		</tr>
		<tr>
			<td cell="21"><?= $replace[$game->cell21] ?></td>
			<td cell="22"><?= $replace[$game->cell22] ?></td>
			<td cell="23"><?= $replace[$game->cell23] ?></td>
		</tr>
		<tr>
			<td cell="31"><?= $replace[$game->cell31] ?></td>
			<td cell="32"><?= $replace[$game->cell32] ?></td>
			<td cell="33"><?= $replace[$game->cell33] ?></td>
		</tr>
	</table>


	<script type="text/javascript">
		$(document).ready(function(){
			setInterface(<?= $game->active ? 'true' : 'false' ?>);
	<?php if ($game->active): ?>
				$('.gameboard td').click(function(){
					$.get('<?= CHtml::normalizeUrl(array('setMove')) ?>/cell/'+$(this).attr('cell'), function(){
						refreshGameboard();
					});
				});
	<?php endif; ?>
		});
	</script>
<?php elseif ($game->status == 2): // draw ?>
	<p>Draw game</p><br/>
	<?= CHtml::link('Start new Game', array('newGame')) ?>
<?php else: // have a winner ?>
	<?= $game->winnerName ?> is a winner.<br/>
	<?= CHtml::link('Start new Game', array('newGame')) ?>
<?php endif; ?>
