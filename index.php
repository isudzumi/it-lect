<?php
$roomAry = [1112, 1111, 1110];
$deskline = [			//1列の長さ
	1112 => [5, 7, 7],
	1111 => [5, 7, 6],
	1110 => [5, 7, 7],
];

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
		<div class="board">スクリーン</div>
		<div id="pc-<?=h($room)?>" class="pc">教員PC</div>

			<div class='desk-area'>
			<?php
				$fstL = $deskline[$room][0];
				$secL = $deskline[$room][1];
				$thdL = $deskline[$room][2];
			?>
			<?php for($i = 1; $i<=($fstL+$secL+$thdL)*2; $i++):?>
				<?php if($i == 1 || $i == 1+($fstL*2) || $i == 1+($fstL+$secL)*2):					//the number of the upper left desk ?>

				<div class='desk-block desk-right'>
					<div class='column right'>
				<?php endif; ?>
				<?php if($i == 1+$fstL || $i == 1+($fstL*2)+$secL || $i == 1+(($fstL+$secL)*2)+$thdL):	 		//the number of the upper right desk ?>

					</div>
					<div class='column left'>
				<?php endif; ?>
				<?php $stat = (h($result[$i-1]['status']) == 0) ? "" : 'style="background-color: yellow"' ; ?>

							<div class="cell" ><?=$i?></div>
				<?php if($i == $fstL*2 || $i == ($fstL+$secL)*2 || $i == ($fstL+$secL+$thdL)*2):			//the number of the lower left desk ?>

					</div>
				</div>
				<?php endif; ?>
			<?php endfor; ?>
			</div>

			<?php $dr = h($room) == 1110 ? "door-left" : "door-right" ?>
			
			<div class="door-area <?=h($room==1110) ? "door-area-left" : "door-area-right" ?>">
				<div class="door wall <?=$dr?>">前方ドア<?=(h($room) == 1111) ? "(※締切)" : "" ?></div>
				<div class="door <?=$dr?>">後方ドア<?=(h($room) != 1111) ? "(※締切)" : "" ?></div>
			</div>
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

