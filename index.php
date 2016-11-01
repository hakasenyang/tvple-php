<?php
	include_once '_function.php';
	$aa = new Tvple();
	$url = $_GET['n'];
	$stream = (intval($_GET['s']) >= 1) ? true : false;
	if($url) {
		$tvpleurl = 'http://tvple.com/';
		if(substr($url,0,strlen($tvpleurl)) == $tvpleurl) $url = substr($url, strpos($url, $tvpleurl) + strlen($tvpleurl));
		if(!is_numeric($url)) echo 'Error... (URL Error)';
		else
		{
			$a = $aa->Streaming($url, $stream);
			if(!$a)
				echo 'Error...';
			else exit;
		}
	}
?>
	<form method="GET" autocomplete="off">
		티비플 주소 입력 : <input type="text" name="n" id="n"><br>
		--주소 형식--<br>
		ex1) http://tvple.com/1234<br>
		ex2) 1234<br>
		<label><input type="radio" name="s" value="0" checked>다운로드</label><label><input type="radio" name="s" value="1">스트리밍</label><br>
		<a href='https://noref.tk/?http://tvple.com/' target='_blank'>티비플 접속해서 주소 가져오기</a><br>
		<input type="submit" value="전송"><br><br>
		<a href='help.php'>동작 원리</a><br>
		<a href='tvple_os.zip'>Open Source! (PHP + php_curl) --- 소스 더러움.</a>
	</form>