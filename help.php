<?php
	// .PHP_EOL.
	$data = '동작 원리'.PHP_EOL.
		'1. tvple.com/1234 를 들어 가서 소스를 본다.'.PHP_EOL.
		'2. data-meta=" 를 찾아서 주소로 들어간다.'.PHP_EOL.
		'3. "mp4_avc": "http://v.kr.kollus.com/sr? 부분이 있는데 Redirect 되는 부분을 전부 찾는다.'.PHP_EOL.
		'4. 영상 주소가 보인다. (Redirect 는 한 2~3번 하면 Real URL 이 보일 것이다.)'.PHP_EOL.
		'5. 다운로드 하는 소스를 만들어준다. (Redirect 끝난 부분을 하면 된다.)'.PHP_EOL.
		'&nbsp;- http://ultramonster.video.kr.kollus.com/kr/media-file.mp4 식으로 나온다.'.PHP_EOL.
		'6. 참 쉽죠잉~';
	echo nl2br($data,0);