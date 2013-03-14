<?php
class browser {
	private $fileName, $configArr, $errorNo = null, $errorText = null, $configFileLastModTime;
	function __construct($wid) {
		$this->wid = $wid;
		$this->fileName = 'work_content_data/wid_' . $this->wid . '_data';
		$r = $this->readConfigFile ();
		if ($r != TRUE) {
			$this->errorNo = 500;
			$this->errorText = "读取配置文件失败";
		}
	}
	
	function getErrorInfo() {
		if ($this->errorNo != null) {
			return array ($this->errorNo, $this->errorText );
		} else {
			return FALSE;
		}
	}
	//读取配置文件
	private function readConfigFile() {
		if (file_exists ( $this->fileName )) {
			if (! $fp = fopen ( $this->fileName, 'r+b' )) {
				return FALSE;
			}
		} else { //如果配置文件不存在，则先创建配置文件
			$fp = fopen ( $this->fileName, "w+b" ); //打开文件指针，创建文件
			if (is_readable ( $this->fileName )) {
				if (! fwrite ( $fp, "[user]\nuname=\npwd=\n[cookies]" )) {
					return FALSE;
				}
			} else {
				return FALSE;
			}
		}
		$lines = file ( $this->fileName );
		foreach ( $lines as $k => $c ) {
			$c = trim ( $c );
			if (isset ( $c )) {
				if (preg_match ( "/^\[(.+)\]$/", $c, $matchArr )) {
					if ($matchArr [0] == '[user]') {
						$arrKey = '[user]';
					} elseif ($matchArr [0] == '[cookies]') {
						$arrKey = '[cookies]';
					} else {
						$arrKey = $c;
					}
					$this->configArr [$arrKey] = null;
				} else {
					$cItemArr = preg_split ( '/=/', $c, 2 );
					if (count ( $cItemArr ) == 2) {
						$cValueName = $cItemArr [0];
						$cValue = $cItemArr [1];
						$this->configArr [$arrKey] [$cValueName] = $cValue;
					} else {
						$this->configArr [$arrKey] [$c] = "";
					}
				}
			}
		}
		
		//记录配置文件的修改时间
		$fstat = fstat ( $fp );
		$this->configFileLastModTime = $fstat ['mtime'];
		
		//删除无用的cookie（到期的cookie、超过指定时间的session cookie）
		$oldCookiesArr = $this->getAllCookies ();
		for($i = 0; $i < count ( $oldCookiesArr ); $i ++) {
			if ($oldCookiesArr [$i] ['expire'] == 'session') { //如果是session cookie，则
				if (time () - $this->configFileLastModTime > 7200) { //如果配置文件的修改时间距离现在已经超过了2小时，则将session cookie删除掉
					unset ( $this->configArr ['[cookies]'] [$oldCookiesArr [$i] ['name']] );
				}
			} else { //如果不是session cookie，则判断是否已到期
				if ($oldCookiesArr [$i] ['expire'] < time ()) { //如果已到期，则将其删除
					unset ( $this->configArr ['[cookies]'] [$oldCookiesArr [$i] ['name']] );
				}
			}
		}
		
		fclose ( $fp );
		return TRUE;
	}
	
	private function writeConfigFile_getText($t1, &$t2) {
		foreach ( $t1 as $k => $c ) {
			if (is_array ( $c )) {
				array_push ( $t2, $k );
				$this->writeConfigFile_getText ( $c, $t2 );
			} elseif ((preg_match ( "/^\[(.+)\]$/", $k ))) {
				array_push ( $t2, $k );
			} else {
				array_push ( $t2, $k . '=' . $c );
			}
		}
	}
	//写入配置文件
	function writeConfigFile() {
		$textArr = array ();
		/*function writeConfigFile_getText($t1, &$t2) {
			foreach ( $t1 as $k => $c ) {
				if (is_array ( $c )) {
					array_push ( $t2, $k );
					$this->writeConfigFile_getText ( $c, $t2 );
				} elseif ((preg_match ( "/^\[(.+)\]$/", $k ))) {
					array_push ( $t2, $k );
				} else {
					array_push ( $t2, $k . '=' . $c );
				}
			}
		}*/
		
		$this->writeConfigFile_getText ( $this->configArr, $textArr );
		if (! $fp = fopen ( $this->fileName, "w+b" )) {
			return FALSE;
		}
		if (! fwrite ( $fp, implode ( "\n", $textArr ) )) {
			return FALSE;
		}
		fclose ( $fp );
		return TRUE;
	}
	
	//保存用户名和密码
	function saveUnamePwd($uname, $pwd) {
		$this->configArr ['[user]'] ['uname'] = $uname;
		$this->configArr ['[user]'] ['pwd'] = $pwd;
		if (! $this->writeConfigFile ()) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	//设置指定的cookie
	function setCookie($cn, $cv, $cdomain='-', $cpath='/', $ce = 'session', $csecure = 0) { //cookie名，cookie值，cookie生效域名，cookie生效路径，cookie过期时间（session则为随浏览器关闭而自动删除的cookie），cookie是否为https专用（默认不是）
		$this->configArr ['[cookies]'] [$cn] = $cv . "\t" . $cdomain . "\t" . $cpath . "\t" . $ce . "\t" . $csecure;
	}
	
	function getUserName() {
		return $this->configArr ['[user]'] ['uname'];
	}
	//获取指定的cookie的值和过期时间，返回数组
	function getCookie($cn) {
		$r = array ();
		if (isset ( $this->configArr ['[cookies]'] [$cn] )) {
			$cArr = explode ( "\t", $this->configArr ['[cookies]'] [$cn] );
			$r ['cn'] = $cn;
			$r ['cv'] = $cArr [0];
			$r ['cd'] = $cArr [1];
			$r ['cp'] = $cArr [2];
			$r ['ce'] = $cArr [3];
			$r ['cs'] = $cArr [4];
			return $r;
		} else {
			return FALSE;
		}
	}
	//获取配置文件中存储的所有cookie，返回数组
	function getAllCookies() {
		if (count ( $this->configArr ['[cookies]'] ) > 0) {
			$i = 0;
			foreach ( $this->configArr ['[cookies]'] as $key => $value ) {
				$temp = $this->getCookie ( $key );
				$str [$i] ['name'] = $temp ['cn'];
				$str [$i] ['value'] = $temp ['cv'];
				$str [$i] ['domain'] = $temp ['cd'];
				$str [$i] ['path'] = $temp ['cp'];
				$str [$i] ['expire'] = $temp ['ce'];
				$str [$i] ['secure'] = $temp ['cs'];
				$i ++;
			}
			return $str;
		} else {
			return FALSE;
		}
	}
	
	//删除配置文件中指定的一个cookie
	function deleteCookie($cn) {
		unset ( $this->configArr ['[cookies]'] [$cn] );
	}
	//删除配置文件中的所有cookie
	function deleteAllCookies() {
		$oldCookiesArr = $this->getAllCookies ();
		for($i = 0; $i < count ( $oldCookiesArr ); $i ++) {
			unset ( $this->configArr ['[cookies]'] [$oldCookiesArr [$i] ['name']] );
		}
		if ($this->writeConfigFile ()) {
			return count ( $oldCookiesArr );
		} else {
			return - 1;
		}
	}
	private function checkCookie($cArr,$url){	//检查cookie是否与指定的域名信息相符
		$url=strtolower($url);
			
		$cArr['cs']?$protocol='https':$protocol='http';
		if(parse_url ( $url,PHP_URL_SCHEME )!=$protocol){
			return false;
		}
		if(ltrim($cArr['cd'],'.')==$cArr['cd']){	//如果cookie中的域名不是以.开头的，则
			
			$pointCount=substr_count($cArr['cd'],'.');
			if($pointCount>1){	//如果cookie中的域名中含有超过1个.号，则
				if($cArr['cd']!=parse_url ( $url,PHP_URL_HOST )){	//如果域名不相等
					return false;
				}
			}
			if($pointCount==1){	//如果cookie中的域名中含有1个.号，则
				$host=parse_url ( $url,PHP_URL_HOST );
				$pos=strrpos ( $host, $cArr['cd'] );
				if($pos===FALSE || $pos+strlen($cArr['cd'])!=strlen($host)){
					return false;
				}
			}
		}else{	//否则如果是以.开头的
			$host=parse_url ( $url,PHP_URL_HOST );
			$pos=strrpos ( $host, ltrim($cArr['cd'],'.'));
			if($pos===FALSE || $pos+strlen($cArr['cd'])-1!=strlen($host)){
				return false;
			}
		}
		if($cArr['cp']!='/' && strpos(parse_url ( $url,PHP_URL_PATH ),$cArr['cp'])!==0){
			return false;
		}
		return true;
	}
	private function createCookieStr($cookieArr, $url) { //创建cookie串，从配置文件中读取，准备发送
		foreach ( $cookieArr as $key => $value ) {
			$temp = $this->getCookie ( $key );
			if($this->checkCookie($temp,$url)==true){	//如果cookie与指定的域名信息相符
				$str [] = $temp ['cn'] . '=' . $temp ['cv'];
			}
		}
		return implode ( '; ', $str );
	}
	
	private function saveCookies($cookieArr, $url, $_this) { //从返回的数据中获取cookies，并保存至配置文件中
		$i2 = 0;
		
		while ( $cookieArr [$i2] ) {
			$temp = explode ( ";", $cookieArr [$i2] );
			
			$value='';
			$exp='session';
			$domain=null;
			$secure=0;
			$path='/';
			
			$i = 0;
			while ( $temp [$i] ) {
				$temp2 = explode ( "=", trim ( $temp [$i] ), 2 );
				if (strtolower ( $temp2 [0] ) == 'expires') {
					$exp = strtotime ( trim ( $temp2 [1]) );
				} elseif (strtolower ( $temp2 [0] ) == 'domain'){
					$domain = strtolower ( trim ( $temp2 [1]) );
				} elseif (strtolower ( $temp2 [0] ) == 'secure'){
					$secure = 1;
				} elseif (strtolower ( $temp2 [0] ) == 'path'){
					$path = strtolower ( trim ( $temp2 [1]) );
				} elseif ($i == 0) {
					$name = $temp2 [0];
					$value = $temp2 [1];
				}
				$i ++;
			}
			if($domain==null){
				$domain=parse_url($url,PHP_URL_HOST);
			}
			
			if (isset ( $name )) {
				if ($exp!='session' && time () > $exp) {	//如果设置了过期时间，并且当前时间大于过期时间，则说明该cookie已过期，须执行删除
					$_this->deleteCookie ( $name );
				}
				$_this->setCookie ( $name, $value,$domain,$path,$exp,$secure);
			}
			$i2 ++;
		}
	}
	
	private function httpSend2($url, $method, $cookieStr = null, $post_data = null, $referrer = null ) { //URL，方法（GET/POST），COOKIE字符串，POST时使用的DATA，设置的来路
		$ch = curl_init ();
		
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_BINARYTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_HEADER, true );
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
		curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2) AppleWebKit/535.12 (KHTML, like Gecko) Maxthon/3.0 Chrome/18.0.966.0 Safari/535.12' );
		

		if ($cookieStr != null) {
			curl_setopt ( $ch, CURLOPT_COOKIE, $cookieStr );
		}
		
		if ($referrer != null) {
			curl_setopt ( $ch, CURLOPT_REFERER, $referrer );
		}
		
		if ($method == 'POST') {
			curl_setopt ( $ch, CURLOPT_POST, true );
			if ($post_data != null) {
				curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query ( $post_data ) );
			} else {
				curl_setopt ( $ch, CURLOPT_POSTFIELDS, '' );
			}
		
		}
		
		$resource = curl_exec ( $ch );
		$info = curl_getinfo ( $ch );
		
		$result ['cookiesArr'] = array ();
		$result ['headerArr'] = array ();
		$result ['body'] = null;
		$result ['info'] = $info;
		
		$matchArrNum = preg_match_all ( "/.+:.+[[\r\n]|[\n]]/", substr ( $resource, 0, $info ['header_size'] ), $matchArr );
		if ($matchArrNum > 0) {
			for($i = 0; $i < $matchArrNum; $i ++) {
				if (strstr ( $matchArr [0] [$i], 'Set-Cookie:' )) {
					$temp = explode ( " ", $matchArr [0] [$i], 2 );
					array_push ( $result ['cookiesArr'], trim ( $temp [1] ) );
				}
				array_push ( $result ['headerArr'], trim ( $matchArr [0] [$i] ) );
			}
		}
		/*echo $resource;
		print_r($matchArr);
		print_r($result ['cookiesArr']);*/
		if(strpos($result ['info']['content_type'], 'image')!==false){	//如果文件类型是图片，则进行BASE64转码
			$result ['body'] = base64_encode( substr ( $resource, $info ['header_size'] ));
		}elseif(strpos($result ['info']['content_type'], 'text')!==false){	//如果文件类型是文本，则转换为UTF-8编码
			$result ['body'] = mb_convert_encoding ( trim ( substr ( $resource, $info ['header_size'] ) ), "UTF-8", "EUC-CN,UTF-8" );
		}else{
			$result ['body'] = substr ( $resource, $info ['header_size'] );
		}
		curl_close ( $ch );
		return $result;
	}
	//打开指定的URL，并自动加入cookies、保存返回的cookies，然后返回收到的数据
	function openUrl($url, $method, $postValueArr = array(),$referrer=null) { //$postValueArr必须是一个关联数组，如 array("p1"=>'value') 表示 p1=value
		if (count ( $this->configArr ['[cookies]'] ) > 0) {
			$cookieStr = $this->createCookieStr ( $this->configArr ['[cookies]'], $url );
		} else {
			$cookieStr = '';
		}
		
		//$cookieStr="NEW_USER_PRO:81051=0; NEW_USER_PHOTO:81051=0; FRAM_HOME:81051=0; yy365sessiond=249e7d70-8f7d-4cd6-92bd-fdf38b8d5ccf; JSESSIONID=996123BE8503B0EBBB59A09CB8DA2049; yt_r_e=81051";
		//echo "<p>[$cookieStr]</p>";
		if ($method == 'GET') {
			if (! $result = $this->httpSend2 ( $url, $method, $cookieStr, null, $referrer )) {
				return FALSE;
			}
		} elseif ($method == 'POST') {
			if (count ( $postValueArr ) > 0) {
				if (! $result = $this->httpSend2 ( $url, $method, $cookieStr, $postValueArr, $referrer )) {
					return FALSE;
				}
			} else {
				if (! $result = $this->httpSend2 ( $url, $method, $cookieStr, null, $referrer )) {
					return FALSE;
				}
			}
		}
		
		if (count ( $result ['cookiesArr'] ) > 0) {
			$this->saveCookies ( $result ['cookiesArr'], $url, $this );
		}
		//保存所有的配置数据至配置文件中
		if (! $this->writeConfigFile ()) {
			return FALSE;
		}
		//print_r($result);
		return $result;
	}
}
?>