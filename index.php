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
		<a href="#1112">1112教室</a>
		<a href="#1111">1111教室</a>
		<a href="#1110">1110教室</a>
	</nav>
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

					echo "\t\t<div class='cell'>".$i."</a></div>\n";

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
		</div>48
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
					var i = $(this).html();
					$("#modal-comment").append("<p id='comment'>"+i+"番の座席を使用中にしますか？</p>");
					$("#modal").fadeIn("slow").removeClass("is-hide");
					$("#modal-overlay, #modal-close").unbind().click(function(){
						fadeout();
					});
					$("#submit").click(function(){
						$(this).css("background-color","yellow");
						fadeout();
					});
				});
			});

			function fadeout(){
				$("#modal, #modal-overlay").fadeOut("slow", function(){
					$("#modal-overlay, #comment").remove();
				});
			}
</script>

