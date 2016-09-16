<!DOCTYPE html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="./style.css">
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
	<div class="pc" id="pc-1112">教室PC</div>
		<div class="desk-area">
			<?php
			for($i=1; $i<=38; $i++){
				if($i == 1 || $i == 11 || $i == 25){
					echo "<div class='desk-block'>\n", "\t<div class='column right'>\n";
				}
				if($i == 6 || $i == 18 || $i == 32) {
					echo "\t</div>\n", "\t<div class='column left'>\n";
				}

					echo "\t\t<div class='cell'>".$i."</div>\n";

				if($i == 10 || $i == 24 || $i == 38){
					echo "\t</div>\n", "</div>\n";
				}
			}
			?>
		</div>
		<div class="door-area">
			<div class="door wall">前方ドア</div>
			<div class="door">後方ドア(※締切)</div>
		</div>
	</div>
	<div id="1111" class="room">
	<div class="board">スクリーン</div>
	<div class = "pc" id="pc-1111">教室PC</div>
		<div class="desk-area">
			<?php
			for($i=1; $i<=36; $i++){
				if($i == 1 || $i == 11 || $i == 25){
					echo "<div class='desk-block'>\n", "\t<div class='column right'>\n";
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
		<div class="door-area">
			<div class="door wall">前方ドア(※締切)</div>
			<div class="door">後方ドア</div>
		</div>
	</div>

	<div id="1110" class="room">
	<div class="board">スクリーン</div>
	<div class="pc" id="pc-1112">教室PC</div>
		<div class="desk-area">
			<?php
			for($i=1; $i<=38; $i++){
				if($i == 1 || $i == 11){
					echo "<div class='desk-block'>\n", "\t<div class='column right'>\n";
				} elseif ($i == 25) {
					echo "<div class='desk-block' id='desk-back'>\n", "\t<div class='column right'>\n";
				}
				if($i == 6 || $i == 18 || $i == 32){
					echo "\t</div>\n", "\t<div class='column left'>\n";
				}

					echo "\t\t<div class='cell'>".$i."</div>\n";

				if($i == 10 || $i == 24 || $i == 38){
					echo "\t</div>\n", "</div>\n";
				}
			}
			?>
		</div>
		<div class="door-area">
			<div class="door wall">前方ドア</div>
			<div class="door">後方ドア(※締切)</div>
		</div>
	</div>
</div>
</body>

