<h1>Добро пожаловать, <?=$name?></h1>
<p>игра начнется при подключении другого игрока.</p>

<script type="text/javascript">
(function poll(){
    $.ajax({ url: "<?=CHtml::normalizeUrl(array('waitForPlayerEvent'))?>", success: function(data){
            if(data.status == 1) window.location.reload();
    }, dataType: "json", complete: poll, timeout: <?=Yii::app()->params['longPoolTimeout']?>, async: true});
})();    
</script>