<?php
$roomAry = [1112, 1111, 1110];
putenv("CLEARDB_DATABASE_URL=mysql://be39339f7ce21f:18063413@us-cdbr-iron-east-04.cleardb.net/heroku_03fc01bc4aafcb0?reconnect=true");

$db = parse_url(getenv('CLEARDB_DATABASE_URL'));
$db['dbname'] = ltrim($db['path'], '/');
$dsn = "{$db['scheme']}:host={$db['host']};dbname={$db['dbname']};charset=utf8";

try {
	$db = new PDO($dsn, $db['user'], $db['pass']);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql = 'SELECT * FROM desk where room = 1112';
	$prepare = $db->prepare($sql);

	$prepare->execute();
	$result = $prepare->fetchAll(PDO::FETCH_ASSOC);
	//print_r(h($result));

} catch (PODException $e) {
	echo "Error: " . h($e->getMessage());
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
	<?php foreach($roomAry as $room): ?>
		<a href="#<?=h($room)?>"><?=h($room)?>教室</a>
	<?php endforeach; ?>
	</nav>

	<?php foreach($roomAry as $room): ?>
	<div id="<?=h($room)?>" class="room">
		<div class='board'>スクリーン</div>
		<div class='pc'>教員PC</div>

			<div class='desk-area'>
			</div>

			<?php $dr = h($room) == 1110 ? "door-left" : "door-right" ?>
			
			<div class="door-area <?=h($room==1110) ? "door-area-left" : "door-area-right" ?>">
				<div class="door wall <?=$dr?>">前方ドア<?=(h($room) == 1111) ? "(※締切)" : "" ?></div>
				<div class="door <?=$dr?>">後方ドア<?=(h($room) != 1111) ? "(※締切)" : "" ?></div>
			</div>
		</div>

	<?php endforeach; ?>

	<div id="1112" class="room">
	<div class="board">スクリーン</div>
	<div class="pc" id="pc-1112">教員PC</div>
		<div class="desk-area">
			<?php
			for($i=1; $i<=38; $i++){
				if($i == 1 || $i == 11 || $i == 25){	//the number of the upper left desk
					echo "<div class='desk-block desk-right'>\n", "\t<div class='column right'>\n";
				}
				if($i == 6 || $i == 18 || $i == 32) {	//the number of the upper right desk
					echo "\t</div>\n", "\t<div class='column left'>\n";
				}

				$stat = (h($result[$i-1]['status']) == 0) ? "" : ' style="background-color: yellow"' ;
					echo "\t\t<div class='cell'".$stat.">".$i."</a></div>\n";

				if($i == 10 || $i == 24 || $i == 38){	//the number of the lower left desk
					echo "\t</div>\n", "</div>\n";
				}
			}
			?>
		</div>
		<div class="door-area door-area-right">
			<div class="door wall door-right">前方ドア</div>
			<div class="door door-right">後方ドア(※締切)</div>
		</div>
	</div>
	<div id="1111" class="room">
	<div class="board">スクリーン</div>
	<div class = "pc" id="pc-1111">教員PC</div>
		<div class="desk-area">
			<?php
			for($i=1; $i<=36; $i++){
				if($i == 1 || $i == 11){
					echo "<div class='desk-block desk-right'>\n", "\t<div class='column right'>\n";
				} elseif ($i == 25){
					echo "<div class='desk-block desk-right' id='desk-back'>\n", "\t<div class='column right'>\n";
				}
				if($i == 6 || $i == 18 || $i == 31){
					echo "\t</div>\n", "\t<div class='column left'>\n";
				}

					echo "\t\t<div class='cell'>".$i."</div>\n";

				if($i == 10 || $i == 24 || $i == 36){
					echo "\t</div>\n", "</div>\n";
				}
			}
			?>
		</div>
		<div class="door-area door-area-right">
			<div class="door wall door-right">前方ドア(※締切)</div>
			<div class="door door-right">後方ドア</div>
		</div>
	</div>

	<div id="1110" class="room">
	<div class="board">スクリーン</div>
	<div class="pc" id="pc-1110">教員PC</div>
		<div class="door-area door-area-left">
			<div class="door wall door-left">前方ドア</div>
			<div class="door door-left">後方ドア(※締切)</div>
		</div>
		<div class="desk-area door-right">
			<?php
			for($i=1; $i<=38; $i++){
				if($i == 1 || $i == 11 || $i == 25){
					echo "<div class='desk-block desk-left'>\n", "\t<div class='column left'>\n";
				}
				if($i == 6 || $i == 18 || $i == 32){
					echo "\t</div>\n", "\t<div class='column right'>\n";
				}

					echo "\t\t<div class='cell'>".$i."</div>\n";

				if($i == 10 || $i == 24 || $i == 38){
					echo "\t</div>\n", "</div>\n";
				}
			}
			?>
		</div>
	</div>
	<div id="modal" class="is-hide">
		<div id="modal-comment"></div>
		<button type="submit" id="submit">OK</button> <button id="modal-close">Cancel</button> 
	</div>

</div>
</body>

<script>
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
					var color = (action == "toUsing") ? "yellow" : "" ;
					c.css("background-color", color);
					fadeout();
				}
				});
			}

			function fadeout(){
				$("#modal, #modal-overlay").fadeOut("slow", function(){
					$("#modal-overlay, #comment").remove();
				});
			}
</script>

<?php
			$updatesql = "update desk set status = 1";
?>

