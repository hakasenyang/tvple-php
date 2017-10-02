<?php
/**
 * TVPLE 다운로드 클래스...
 * php_curl 이 필요합니다.
 * Made by hakase (contact@hakase.kr)
 * https://hakase.kr/
 */
	class Tvple {
		private $httph = 'DEXT5U5928d310';
		/**
		 * [splits description]
		 * @param  [type]  $data  [description]
		 * @param  [type]  $first [description]
		 * @param  [type]  $end   [description]
		 * @param  integer $num   [description]
		 * @return [type]         [description]
		 */
		private function splits($data, $first, $end, $num = 1)
		{
			$temp = explode($first, $data);
			$temp = explode($end, $temp[$num]);
			$temp = $temp[0];
			return $temp;
		}
		private 	function webecho($ch, $string) {
			$length = strlen($string);
			echo $string;
			ob_flush();
			flush();
			return $length;
		}
		private function WEBParsing($url,$bodychk=false,$paramType="GET",$param="",$cookie="")
		{
			$ch = curl_init();
			$opts = array(CURLOPT_RETURNTRANSFER => true,
				CURLOPT_URL => $url,
				CURLOPT_TIMEOUT => 10,
				CURLOPT_CONNECTTIMEOUT => 5,
				CURLOPT_USERAGENT => $this->httph,
				CURLOPT_COOKIE => $cookie,
				CURLOPT_HEADER => 0
				);

			if ($paramType == 'POST')
			{
				$opts[CURLOPT_POST] = true;
				$opts[CURLOPT_POSTFIELDS] = $param;
			}

			if($bodychk === true)
			{
				$opts[CURLOPT_HEADER] = 1;
				$opts[CURLOPT_NOBODY] = $bodychk;
			}
			curl_setopt_array($ch, $opts);
			$data = curl_exec($ch);
			curl_close($ch);
			return ($data) ? $data : false;
		}
		private function Download($url,$range=0)
		{
			$ch = curl_init();
			$opts = array(CURLOPT_RETURNTRANSFER => true,
				CURLOPT_URL => $url,
				CURLOPT_TIMEOUT => 60,
				CURLOPT_CONNECTTIMEOUT => 30,
				CURLOPT_USERAGENT => $this->httph,
				CURLOPT_COOKIE => $cookie,
				CURLOPT_NOPROGRESS => false,
				CURLOPT_WRITEFUNCTION => array($this, 'webecho'),
				CURLOPT_HEADER => 0
				);
			if ($range) $opts[CURLOPT_RANGE]=$range;
			curl_setopt_array($ch, $opts);
			$data = curl_exec($ch);
			curl_close($ch);
			return ($data) ? $data : false;
		}
		public function Streaming($link, $streaming=false)
		{
			$link = abs(intval($link));
			if(!is_numeric($link)) exit;
			$data = $this->WEBParsing('http://tvple.com/'.$link);
			$data = $this->WEBParsing('http://tvple.com/'.$this->splits($data, '<a href="/', '"'));

			$name = htmlspecialchars_decode($this->splits($data, '"og:title" content="','"'));

			$api = $this->splits($data,'data-meta="','"');
			$data = $this->WEBParsing($api);
			$link = $this->splits($data,'"mp4_avc": "','"');

			if(!$link) return false;
			// 2016-02-28 수정
			$data = $this->WEBParsing($link,true);
			$size = $length = $this->splits($data, 'Content-Length: ', PHP_EOL);

			$start  = 0;               // Start byte
			$end   = $size - 1;       // End byte

			if (isset($_SERVER['HTTP_RANGE'])) {
				$rangeok=true;
			    $c_start = $start;
			    $c_end  = $end;
			    list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
			    if (strpos($range, ',') !== false) {
			        header($_SERVER['SERVER_PROTOCOL'].' 416 Requested Range Not Satisfiable');
			        header('Content-Range: bytes '.$start.'-'.$end.'/'.$size);
			        exit;
			    }
			    if ($range == '-') {
			        $c_start = $size - substr($range, 1);
			    }else{
			        $range  = explode('-', $range);
			        $c_start = $range[0];
			        $c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
			    }
			    $c_end = ($c_end > $end) ? $end : $c_end;
			    if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
			        header($_SERVER['SERVER_PROTOCOL'].' 416 Requested Range Not Satisfiable');
			        header('Content-Range: bytes '.$start.'-'.$end.'/'.$size);
			        exit;
			    }
			    $start  = $c_start;
			    $end    = $c_end;
			    $length = $end - $start + 1;
			    header($_SERVER['SERVER_PROTOCOL'].' 206 Partial Content');
			}
			header('Accept-Ranges: 0-'.$length);
			header('Content-Range: bytes '.$start.'-'.$end.'/'.$size);
			header('Content-Length: '.$length);

			if ($streaming) header('Content-Type: video/mp4'); else header('Content-Type: video/MP2T');
			header('Accept-Ranges: bytes');
			header('Cache-Control: no-cache');
			$filename = $name.'.mp4';
			header('Content-Disposition: filename="'.$filename.'"');

			set_time_limit(0);
			ob_start();
			if ($rangeok)
				$this->Download($link,$start.'-'.$end);
			else
				$this->Download($link);
			ob_end_flush();
		}
	}
