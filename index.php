<?php
require_once __DIR__.'/vendor/autoload.php';

//phpdotenv
use Dotenv\Dotenv;
$dotenv = new Dotenv(__DIR__);
$dotenv->load();

//monolog
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
$log = new Logger('MONOLOG_TEST');
$handler = new StreamHandler('./app.log', Logger::DEBUG);
$log->pushHandler($handler);

//firebase-php
$dburl  = getenv('databaseURL');
$token = getenv('databaseSecret');
$firebase = new \Firebase\FirebaseLib($dburl, $token);

$ary = $firebase->get('/room');
$ary = json_decode($ary, true);
$log->addDebug('Debug');

$deskline = [			//机の縦1列の長さ
	1112 => [5, 7, 7],
	1111 => [5, 7, 6],
	1110 => [5, 7, 7],
];

$roomOrder = [1112, 1111, 1110];//データベースからただ持ってくるだけだと1110教室から表示されてしまうため順番を指定する

require_once("get.php");

?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>試験教室 座席表</title>
<link rel="stylesheet" href="//fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="//code.getmdl.io/1.1.3/material.green-light_green.min.css" />
<link rel="stylesheet" type="text/css" href="./style/style.css">
<script defer src="//code.getmdl.io/1.1.3/material.min.js"></script>
<script src="//code.jquery.com/jquery-3.1.0.js"></script>
</head>

<body>
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
<header class="mdl-layout__header">
	<div class="mdl-layout__header-row">
		<span class="mdl-layout-title"><h3>試験教室 座席表</h3></span>
	</div>
	<div class="mdl-layout__tab-bar mdl-js-ripple-effect">
	<?php foreach($roomOrder as $room): ?>
		<a href="#room<?=$room?>" class="mdl-layout__tab <?=($room)==1112 ? "is-active" : ""?>"><?=$room?>教室</a>
	<?php endforeach; ?>
	</div>
</header>
<div class="mdl-layout__drawer">
	<span class="mdl-layout-title">試験教室</span>
	<nav class="mdl-navigation">
		<a class="mdl-navigation__link" href="https://docs.google.com/spreadsheets/d/1LDDefOWRvG-Tr6vlrqGLQe3mewMBNZQkcrOMANyJ-AU/edit?usp=sharing" target="_blank">IT-Aご意見帳</a>
		<a class="mdl-navigation__link" href="https://docs.google.com/forms/d/e/1FAIpQLSfNPoLfWMFwKZkba3y50uMj-mLXa0JsZjK1OacPe3K8FyYolQ/viewform" target="_blank">IT講習会不正行為疑い事例報告フォーム</a>
		<a class="mdl-navigation__link" href="https://github.com/isudzumi/it-lect" target="_blank">開発元</a>
		<a class="mdl-navigation__link">設定</a>
		<button class="mdl-button mdl-js-button mdl-js-ripple-effect" id="reset">Reset</button>
	</nav>
</div>
<main class="mdl-layout__content">

	<?php foreach($roomOrder as $room): ?>
	<section id="room<?=$room?>" class="mdl-layout__tab-panel <?=($room)==1112 ? "is-active" : ""?>">
	<div class="room page__content">
		<div class="board">スクリーン</div>
		<div id="pc-<?=$room?>" class="pc">教員PC</div>
		<?php if($ary["$room"]['door']['direction'] == 'left') : ?>
			
			<div class="door-area door-area-left">
				<div class="door wall door-left">前方ドア<?=($ary["$room"]["door"]['front'] == 'close') ? "(※締切)" : "" ?></div>
				<div class="door door-left">後方ドア<?=($ary["$room"]["door"]['back'] == 'close') ? "(※締切)" : "" ?></div>
			</div>
		<?php endif; ?>

		<div class="desk-area" id="desk-<?=$room?>">
		<?php
			$i = 1;
			foreach($ary["$room"]['column'] as $col => $desk):
				$count = count($ary["$room"]['column'][$col]);
				for($j = 1; $j <= $count; $j++):
					if((substr($col, 4) % 2 == 1) && ($j == 1)):
		?>

				<div class='desk-block desk-right'>
					<div class="column <?=($room != 1110) ? 'right' : 'left'?>">
		<?php
					elseif((substr($col, 4) % 2 == 0) && ($j == 1)):
		?>

					</div>
					<div class="column <?=($room != 1110) ? 'left' : 'right'?>">
		<?php
					endif;
		?>
					<?php $bgcolor = ($ary["$room"]['column']["$col"]['desk-'.$i] == 1) ? ' style="background-color: yellow"' : "" ; ?>

						<div class="cell"<?=$bgcolor?>><?=$i?></div>
		<?php
					if((substr($col, 4) % 2 == 0) && ($j == $count)):
		?>

					</div>
				</div>
		<?php
					endif;
					$i++;
				endfor;
			endforeach;
		?>
		</div>
		<?php if($ary["$room"]["door"]['direction'] == 'right'): ?>
		
		<div class="door-area door-area-right">
			<div class="door wall door-right">前方ドア<?=($ary["$room"]["door"]['front'] == 'close') ? "(※締切)" : "" ?></div>
			<div class="door door-right">後方ドア<?=($ary["$room"]["door"]['back'] == 'close')  ? "(※締切)" : "" ?></div>
		</div>
		<?php endif; ?>

	</div>
	</section>
	<?php endforeach; ?>
</main>
</div>
<dialog id="modal" class="is-hide mdl-dialog">
	<div class="mdl-dialog__title" id="modal-comment"></div>
	<div class="mdl-dialog__actions">
	<button type="submit" class="mdl-button mdl-js-button mdl-button--raised mld-js-ripple-effect mdl-button--colored" id="submit">OK</button>
	<button class="mdl-button" id="modal-close">Cancel</button> 
	</div>
</dialog>
</body>

<script>
			$(".desk-block").eq(5).css("margin-top", "calc(9vh + 32px)");

			$(function(){
				$(".cell").click(function(){
					var action = "";
					if($(this).css("background-color") == "rgb(255, 255, 0)")
						action = "toEmpty";
					else if($(this).css("background-color") == "rgb(239, 239, 239)")
						action = "toUsing";
					var data = {
						"room":$(this).parents(".mdl-layout__tab-panel.is-active").attr("id").substring(4),
						"desk":$(this).html(),
						"status":(action == "toUsing") ? 1 : 0
					};
					if(action == "toEmpty")
						$(this).css("background-color", "");
					else if(action == "toUsing")
						$(this).css("background-color", "yellow");

					setstatus(data, $(this));
				});
			});

			$(function(){
				$("#reset").click(function(e){
					var roomN= $(".mdl-layout__tab.is-active").attr("href").substring(5);
					var room = { "room": roomN};
					$.ajax({
						type:'POST',
						url :'reset.php',
						data: room,
						dataType:'text',
						cache:false,
						timeout:10000,
						beforeSend:function(){
							$("#reset").attr('disabled', true);
							$("#reset").css("background-color", "lightgreen");
						}
					}).done(function(data){
						if(data == "success") {
							$("#desk-"+roomN).find(".cell").css("background-color", "");
						}
					}).fail(function(xhr, ts, err){
						console.log(xhr.status);
						console.log(xhr.readyState);
						console.log(xhr.responseText);
						console.log(ts);
						console.log(err.message);
					}).always(function(){
						$("#reset").attr('disabled', false);
						$("#reset").css("background-color", "white");
					});
				});
			});

			//自動更新
			$(function(){
				setInterval(function(){
					$.ajax({
						type:'POST',
						url :'update.php',
					}).done(function(data){
							var obj = JSON.parse(data);
							compare(obj);
					}).fail(function(xhr, ts, err){
						console.log(xhr.status);
						console.log(xhr.readyState);
						console.log(xhr.responseText);
						console.log(ts);
						console.log(err.message);
					});
				}, 60000);
			});

			function compare(update) {
				var cell;
				for(var i in update) {
					for(var j in update[i]){
						cell = $("#desk-"+i).find(".cell").eq(j-1).css("background-color");
						if ((cell == "rgb(211, 211, 211)") && (update[i][j] == 1)) {
							$("#desk-"+i).find(".cell").eq(j-1).css("background-color", "yellow");
						} else if((cell == "rgb(255, 255, 0)") && (update[i][j] == 0)) {
							$("#desk-"+i).find(".cell").eq(j-1).css("background-color", "");
						}
					}
				}
			}

			//画面スワイプ
			var roomAry = [1112, 1111, 1110];
			$(function(){
				$(".mdl-layout__tab-panel").on("touchstart", touchStart);
				$(".mdl-layout__tab-panel").on("touchmove" , touchMove);
				$(".mdl-layout__tab-panel").on("touchend"  , touchEnd);
				
				var pos, dir;

				function touchStart(e) {
					pos = position(e);
					dir = '';
				}

				function touchMove(e) {
					now = position(e);
					if(now - pos > 70) {
						dir = 'right';
					} else if (now - pos < -70) {
						dir = 'left';
					}
				}

				function touchEnd(){
					if(dir == 'right') {
						changeSection("right");
					} else if(dir == 'left') {
						changeSection("left");
					}
				}

				function changeSection(dir){
					var id = $(".mdl-layout__tab-panel.is-active").attr("id");
					if(dir == "right"){
						for(var i = 1; i < roomAry.length ; i++) {
							if("room"+roomAry[i] == id){
								$("#room"+roomAry[i]).removeClass("is-active");
								$("#room"+roomAry[i-1]).addClass("is-active");
								$(".mdl-layout__tab:eq("+i+")").removeClass("is-active");
								$(".mdl-layout__tab:eq("+(i-1)+")").addClass("is-active");
								break;
							}
						}
					} else if(dir == "left"){
						for(var i = 0; i < roomAry.length-1 ; i++) {
							if("room"+roomAry[i] == id){
								$("#room"+roomAry[i]).removeClass("is-active");
								$("#room"+roomAry[i+1]).addClass("is-active");
								$(".mdl-layout__tab:eq("+i+")").removeClass("is-active");
								$(".mdl-layout__tab:eq("+(i+1)+")").addClass("is-active");
								break;
							}
						}
					}
				}

				function position(e) {
					var x = e.originalEvent.touches[0].pageX;
					return x;
				}

			});

			function fadeout(){
				$("#modal, #modal-overlay").fadeOut("slow", function(){
					$("#modal-overlay, #comment").remove();
				});
			}

			function setstatus(data, c){
				$.ajax({
					type:'POST',
					url :'post.php',
					dataType:'text',
					data:data,
					timeout:10000,
					beforeSend:function(){
						c.attr('disabled', true);
					}
				}).fail(function(xhr, ts, err){
					c.css("background-color", data["status"]==1 ? "" : "yellow");
					alert("Fail : "+ts+", "+err.massage);
				}).always(function(){
					c.attr('disabled', false);
				});
			}
 
</script>
