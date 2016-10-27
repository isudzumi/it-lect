<?php
$deskline = [			//机の縦1列の長さ
	1112 => [5, 7, 7],
	1111 => [5, 7, 6],
	1110 => [5, 7, 7],
];
$db = parse_url(getenv('CLEARDB_DATABASE_URL'));
$db['dbname'] = ltrim($db['path'], '/');
$dsn = "{$db['scheme']}:host={$db['host']};dbname={$db['dbname']};charset=utf8";
$db = new PDO($dsn, $db['user'], $db['pass']);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$statAry = [];			//['教室番号']=>['机番号']=>'status( 0 or 1 )'
try {
	$sql = "SELECT room, desk, status FROM desk";
	$prepare = $db->prepare($sql);

	$prepare->execute();
	$result = $prepare->fetchAll(PDO::FETCH_ASSOC);

	//$resultに格納された配列を、教室番号と机番号をキーにした2次元配列に書き換える
	for($i = 0; $i < count($result); $i++){
		if($i != 0 && $result[$i]['room'] == $result[$i-1]['room']){
			$statAry[$result[$i]['room']][$result[$i]['desk']] = $result[$i]['status'];
		} else {
			$statAry += [$result[$i]['room'] => [$result[$i]['desk'] => $result[$i]['status']]];
		}
	}
	
} catch (PODException $e) {
	$iserr = TRUE;
	echo "Error: ". h($e->getMessage());
}

function h($var)
{
    if (is_array($var)) {
	return array_map('h', $var);
    } else {
	return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="./style.css">
<script src="//code.jquery.com/jquery-3.1.0.js"></script>
</head>

<body>
<div id="wrapper">
<h1>試験教室　座席表</h1>
	<nav>
	<?php foreach($deskline as $room => $desk): ?>
		<a href="#<?=h($room)?>"><?=h($room)?>教室</a>
	<?php endforeach; ?>
	</nav>

	<?php foreach($deskline as $room => $desk): ?>
	<div id="<?=h($room)?>" class="room">
		<div class="board">スクリーン</div>
		<div id="pc-<?=h($room)?>" class="pc">教員PC</div>
		<?php if($room == 1110): ?>
			
			<div class="door-area door-area-left">
				<div class="door wall door-left">前方ドア<?=(h($room) == 1111) ? "(※締切)" : "" ?></div>
				<div class="door door-left">後方ドア<?=(h($room) != 1111) ? "(※締切)" : "" ?></div>
			</div>
		<?php endif; ?>

		<div class="desk-area" id="desk-<?=h($room)?>">
			<?php
				//机の列の配列を作る
				$lineAry = [];
				foreach($desk as $value){
					array_push($lineAry, $value);
				}
			?>
			<?php for($i = 1; $i<=($lineAry[0]+$lineAry[1]+$lineAry[2])*2; $i++):?>
				<?php if($i == 1 || $i == 1+($lineAry[0]*2) || $i == 1+($lineAry[0]+$lineAry[1])*2):						//the number of the upper left desk ?>

				<div class='desk-block desk-right'>
				<div class="column <?=($room != 1110) ? 'right' : 'left'?>">
				<?php endif; ?>
				<?php if($i == 1+$lineAry[0] || $i == 1+($lineAry[0]*2)+$lineAry[1] || $i == 1+(($lineAry[0]+$lineAry[1])*2)+$lineAry[2]):	//the number of the upper right desk ?>

					</div>
					<div class="column <?=($room != 1110) ? 'left' : 'right'?>">
				<?php endif; ?>
				<?php $stat = ($statAry["$room"]["$i"] == 1) ? ' style="background-color: yellow"' : "" ; ?>

							<div class="cell"<?=$stat?>><?=$i?></div>
				<?php if($i == $lineAry[0]*2 || $i == ($lineAry[0]+$lineAry[1])*2 || $i == ($lineAry[0]+$lineAry[1]+$lineAry[2])*2):		//the number of the lower left desk ?>

					</div>
				</div>
				<?php endif; ?>
			<?php endfor; ?>
			</div>
			<?php if($room != 1110): ?>
			
			<div class="door-area door-area-right">
				<div class="door wall door-right">前方ドア<?=(h($room) == 1111) ? "(※締切)" : "" ?></div>
				<div class="door door-right">後方ドア<?=(h($room) != 1111) ? "(※締切)" : "" ?></div>
			</div>
			<?php endif; ?>
		</div>

	<?php endforeach; ?>
	<div id="modal" class="is-hide">
		<div id="modal-comment"></div>
		<button type="submit" id="submit">OK</button> <button id="modal-close">Cancel</button> 
	</div>

</div>
</body>

<script>
			$(function(){
				$(".desk-block").eq(5).css("margin-top", "122px");
			});

			$(function(){
				$(".cell").click(function(){
					$("body").append("<div id='modal-overlay'></div>");
					$("#modal-overlay").fadeIn("slow");
					var c = $(this);
					var bgColor = c.css("background-color");
					if(bgColor == "rgb(239, 239, 239)"){		//#efefef
						modalAction(c, "toUsing");
					} else if( bgColor == "rgb(255, 255, 0)"){	//yellow
						modalAction(c, "toEmpty");
					} else {
						fade();
					}
					delete c;
				});
			});

			function modalAction(c, action){
				var msg = (action == "toUsing") ? "使用中" : "空席" ;
				$("#modal-comment").append("<p id='comment'>"+c.html()+"番の座席を"+msg+"にしますか？</p>");
				$("#modal").fadeIn("slow").removeClass("is-hide");
				$(document).off().on("click", function(e){
				var idName = e.target.id;
				if( idName == "modal-overlay" || idName == "modal-close" ){
					fadeout();
				}else if( idName == "submit" ){
					var data = {
						"room": c.parents(".room").attr("id"),
						"desk": c.html(),
						"status":(action == "toUsing") ? 1 : 0 
					};
					var color = (action == "toUsing") ? "yellow" : "" ;
					c.css("background-color", color);
					setstatus(data);
					fadeout();
				}
				});
			}

			function fadeout(){
				$("#modal, #modal-overlay").fadeOut("slow", function(){
					$("#modal-overlay, #comment").remove();
				});
			}

			function setstatus(data){
				$.post(
					'post.php',
					data
				);
			}
</script>
