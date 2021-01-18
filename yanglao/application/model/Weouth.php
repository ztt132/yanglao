<?php
/*
 * @use 微信验证和接受微信post数据接口文件
 * @author 
 * @update_time 2017-5-3日
 * 修复缓存不失效从而带来的问题
 */


/*
 * @use 通过授权获取用户openid
 * */
 namespace app\model;
class Weouth{

	public $auth_appid;
	public $appsecret;

	public $authorizer_access_token;
	public $authorizer_access_token_time;

	public $openid;
	public $sessionKey;
	public $jsapi_ticketkey;
	public $errCode = 40001;
    public $errMsg = "no access";
    public function __construct(){
		$this->auth_appid = 'wx437db912a2e4078e';
		$this->appsecret = 'ff17cfc4a7a2a4127391bdce00feeae8';
		$this->authorizer_access_token=$this->auth_appid."_authorizer_access_token";
		$this->authorizer_access_token_time=$this->auth_appid."_authorizer_access_token_time";
		$this->jsapi_ticketkey=$this->auth_appid."_jsapi_ticket";
		

    }


	
    /*
     * @获取（刷新）授权公众号的令牌
     * */
	public function update_authorizer_access_token()
	{
		$authorizer_access_token_mc=cache($this->authorizer_access_token);
		$authorizer_access_token_time=cache($this->authorizer_access_token_time);
		$now = time();
		if(!$authorizer_access_token_mc || $authorizer_access_token_time<=$now){ //需要刷新缓存 请使用 if(!$authorizer_access_token_mc || 1)
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->auth_appid."&secret=".$this->appsecret;
			$result = $this->http_get($url);
			if ($result){
				$json = json_decode($result,true);
				if (!$json || !empty($json['errcode'])) {
					$this->errCode = $json['errcode'];
					$this->errMsg = "获取authorizer_access_token错误".$json['errmsg'];
					return false;
				}else{
					cache($this->authorizer_access_token,$json["access_token"],$json["expires_in"]-10);
					cache($this->authorizer_access_token_time,$json["expires_in"]+$now-10,$json["expires_in"]-10);
					return $json["access_token"];
				}
			}
			return false;	
		}else {
			return $authorizer_access_token_mc;
		}
	}

	/**
     * 第三方平台开发者的服务器使用登录凭证 code 以及第三方平台的component_access_token 获取 session_key 和 openid
	*code	wx.login 取到的code
	*appid	是	小程序的AppID
	*js_code	是	登录时获取的 code
	*grant_type	是	填authorization_code
	*component_appid	是	第三方平台appid
	*component_access_token	是	第三方平台的component_access_token
     */
	public function get_sessionKey($code){
		$codeurl='https://api.weixin.qq.com/sns/jscode2session?appid='.$this->auth_appid.'&secret='.$this->appsecret.'&js_code='.$code.'&grant_type=authorization_code';
		$result=$this->http_get($codeurl);
		if ($result){
			$json = json_decode($result,true);
			if (!$json || !empty($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			$this->openid=$json['openid'];
			$this->sessionKey=$json['session_key'];
			return $json;
		}
		return false;
	}

	public function check_data($sessionKey,$encryptedData,$iv){	


		//import('aes.wxBizDataCrypt', EXTEND_PATH, '.php');
		require __DIR__ . '/../../extend/aes/wxBizDataCrypt.php';

		$pc = new \WXBizDataCrypt($this->auth_appid, $sessionKey);
		$data='';
		$errCode = $pc->decryptData($encryptedData, $iv, $data );
		if ($errCode == 0) {
			return json_decode($data,true);
		} else {
			$this->errCode = $errCode;
			$this->errMsg = '';
			
			return false;
		}
	}
	/**
     * 获取小程序的二维码
	接口A: 适用于需要的码数量较少的业务场景 接口地址：
     */
    public function getwxacode($page,$width=430,$auto=false,$r=0,$g=0,$b=0){
       $authorizer_access_token=$this->update_authorizer_access_token();
	   if(!$authorizer_access_token){
			return false;
		}
		$MENU_URL="https://api.weixin.qq.com/wxa/getwxacode?access_token=".$authorizer_access_token;
		$data=array(
				'path'=> (string)$page,
				'width'=> (int)$width,
				'auto_color'=>(bool)$auto,
				'line_color'=>array('r'=>(string)$r,'g'=>(string)$g,'b'=>(string)$b)
				);
		$result = $this->http_post($MENU_URL,$this->wx_json_encode($data));
        if ($result)
        {
			if(strpos($result, 'errcode') !== false){
			    $json = json_decode($result,true);
				if (!$json || !empty($json['errcode'])) {
					$this->errCode = $json['errcode'];
					$this->errMsg = $json['errmsg'];
					return false;
				}
			}else{
				return $result;
			}
        }
		return false;
    }
		/**
     * 获取小程序的二维码
	*result=array('header'=>$header,'body'=>$body);header头部信息 body 二进制流
	接口B：适用于需要的码数量极多，或仅临时使用的业务场景
     */
    public function getwxacodeunlimit($scene,$page='',$width=430,$auto=false,$r=0,$g=0,$b=0){
       $authorizer_access_token=$this->update_authorizer_access_token();
	   if(!$authorizer_access_token){
			return false;
		}
		$MENU_URL="https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$authorizer_access_token;
		$data=array(
				'scene'=> (string)$scene,
				'page'=> (string)$page,
				'width'=> (int)$width,
				'auto_color'=>(bool)$auto,
				'line_color'=>array('r'=>(string)$r,'g'=>(string)$g,'b'=>(string)$b)
				);
		$result = $this->http_post($MENU_URL,$this->wx_json_encode($data));
        if ($result)
        {
			if(strpos($result, 'errcode') !== false){
			    $json = json_decode($result,true);
				if (!$json || !empty($json['errcode'])) {
					$this->errCode = $json['errcode'];
					$this->errMsg = $json['errmsg'];
					return false;
				}
			}else{
				return $result;
			}
        }
		return false;
    }
			/**
     * 获取小程序的二维码
	*result=array('header'=>$header,'body'=>$body);header头部信息 body 二进制流
	接口C：适用于需要的码数量较少的业务场景
     */
    public function createwxaqrcode($page,$width=430){
       $authorizer_access_token=$this->update_authorizer_access_token();
	   if(!$authorizer_access_token){
			return false;
		}
		$MENU_URL="https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=".$authorizer_access_token;
		$data=array(
				'path'=> (string)$page,
				'width'=> (int)$width
				);
		$result = $this->http_post($MENU_URL,$this->wx_json_encode($data));
        if ($result)
        {
			if(strpos($result, 'errcode') !== false){
			    $json = json_decode($result,true);
				if (!$json || !empty($json['errcode'])) {
					$this->errCode = $json['errcode'];
					$this->errMsg = $json['errmsg'];
					return false;
				}
			}else{
				return $result;
			}
        }
		return false;
    }
	/************
	*获取小程序模板库标题列表
	*offset 	是 	offset和count用于分页，表示从offset开始，拉取count条记录，offset从0开始，count最大为20。
	*count 	是 	offset和count用于分页，表示从offset开始，拉取count条记录，offset从0开始，count最大为20。
	*******************************/
	public function library_list($offset=0,$count=20){
		$authorizer_access_token=$this->update_authorizer_access_token();
		if(!$authorizer_access_token){
			return false;
		}
		$MENU_URL="https://api.weixin.qq.com/cgi-bin/wxopen/template/library/list?access_token=".$authorizer_access_token;
		$data='{
			"offset":'. (int)$offset.',
			"count":'. (int)$count.'
			}';
		$result = $this->http_post($MENU_URL,$data);
		if ($result){
			$json = json_decode($result,true);
			if (!$json || !empty($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			return $json;
		}
		return false;
	}
		/************
	*获取模板库某个模板标题下关键词库
	*id 	是 	模板标题id，可通过接口获取，也可登录小程序后台查看获取
	*******************************/
	public function library_get($id){
		$authorizer_access_token=$this->update_authorizer_access_token();
		if(!$authorizer_access_token){
			return false;
		}
		$MENU_URL="https://api.weixin.qq.com/cgi-bin/wxopen/template/library/get?access_token=".$authorizer_access_token;
		$data='{
				"id":"'.(string)$id.'"
				}';
		$result = $this->http_post($MENU_URL,$data);
		if ($result){
			$json = json_decode($result,true);
			if (!$json || !empty($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			return $json;
		}
		return false;
	}
	/************
	*组合模板并添加至帐号下的个人模板库
	*id 	是 	模板标题id，可通过接口获取，也可登录小程序后台查看获取
	*list 	是 	开发者自行组合好的模板关键词列表，关键词顺序可以自由搭配（例如[3,5,4]或[4,5,3]），最多支持10个关键词组合
	*******************************/
	public function template_add($id,$list){
		$authorizer_access_token=$this->update_authorizer_access_token();
		if(!$authorizer_access_token){
			return false;
		}
		$MENU_URL="https://api.weixin.qq.com/cgi-bin/wxopen/template/add?access_token=".$authorizer_access_token;
		$data='{
				"id":"'.(string)$id.'", 
				"keyword_id_list":['.(string)$list.'] 
				}';
		$result = $this->http_post($MENU_URL,$data);
		if ($result){
			$json = json_decode($result,true);
			if (!$json || !empty($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			return $json;
		}
		return false;
	}
	/************
	*获取帐号下已存在的模板列表
	*offset 是offset和count用于分页，表示从offset开始，拉取count条记录，offset从0开始，count最大为20。最后一页的list长度可能小于请求的count
	*count 	是 	offset和count用于分页，表示从offset开始，拉取count条记录，offset从0开始，count最大为20。最后一页的list长度可能小于请求的count
	*******************************/
	public function template_list($offset,$count){
		$authorizer_access_token=$this->update_authorizer_access_token();
		if(!$authorizer_access_token){
			return false;
		}
		$MENU_URL="https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token=".$authorizer_access_token;
		$data='{
			"offset":'. (int)$offset.',
			"count":'. (int)$count.'
			}';
		$result = $this->http_post($MENU_URL,$data);
		if ($result){
			$json = json_decode($result,true);
			if (!$json || !empty($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			return $json;
		}
		return false;
	}
		/************
	*删除帐号下的某个模板
	*template_id 	是 	要删除的模板id
	*******************************/
	public function template_del($template_id){
		$authorizer_access_token=$this->update_authorizer_access_token();
		if(!$authorizer_access_token){
			return false;
		}
		$MENU_URL="https://api.weixin.qq.com/cgi-bin/wxopen/template/del?access_token=".$authorizer_access_token;
		$data='{
			"template_id":"'.$template_id.'"
			}';
		$result = $this->http_post($MENU_URL,$data);
		if ($result){
			$json = json_decode($result,true);
			if (!$json || !empty($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			return $json;
		}
		return false;
	}
	/************
	*发送模板消息
		{
		  "touser": "OPENID",  
		  "template_id": "TEMPLATE_ID", 
		  "page": "index",          
		  "form_id": "FORMID",         
		  "data": {
			  "keyword1": {
				  "value": "339208499", 
				  "color": "#173177"
			  }, 
			  "keyword2": {
				  "value": "2015年01月05日 12:30", 
				  "color": "#173177"
			  }, 
			  "keyword3": {
				  "value": "粤海喜来登酒店", 
				  "color": "#173177"
			  } , 
			  "keyword4": {
				  "value": "广州市天河区天河路208号", 
				  "color": "#173177"
			  } 
		  },
		  "emphasis_keyword": "keyword1.DATA" 
		}
	*template_id 	是 	要删除的模板id
	*******************************/
	public function template_send($touser,$template_id,$page='',$form_id,$data,$emphasis_keyword="keyword1.DATA"){
		$authorizer_access_token=$this->update_authorizer_access_token();
		if(!$authorizer_access_token){
			return false;
		}
		$MENU_URL="https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$authorizer_access_token;
		$key_word=array();
		$i=0;
		foreach($data as $value){
			$i++;
			$key_word["keyword".$i]=array("value"=>(string)$value['value'],"color"=>(string)$value['color']);
		}
		$data=array(
			"touser"=>(string)$touser, 
			"template_id"=>(string)$template_id, 
			"page"=>(string)$page, 
			"form_id"=>(string)$form_id, 
			"data"=>$key_word,
			"emphasis_keyword"=>(string)$emphasis_keyword
		);
		$result = $this->http_post($MENU_URL,$this->wx_json_encode($data));
		if ($result){
			$json = json_decode($result,true);
			if (!$json || !empty($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			return $json;
		}
		return false;
	}
		/**
	* POST 请求
	* @param string $url
	* @param array $param
	* @param boolean $post_file 是否文件上传
	* @return string content
	*/
	public function http_post($url,$param,$post_file=false){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		if (is_string($param) || $post_file) {
			$strPOST = $param;
		} else {
			$aPOST = array();
			foreach($param as $key=>$val){
			$aPOST[] = $key."=".urlencode($val);
			}
			$strPOST = join("&", $aPOST);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}
	    /**
     * GET 请求
     * @param string $url
     */
    public function http_get($url){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }
	/**
     * GET 请求图片二进制流
     * @param string $url
     */

	public function http_get_img($url){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
		 curl_setopt($oCurl, CURLOPT_HEADER, TRUE); 
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
      
        if(intval($aStatus["http_code"])==200){
			    $headerSize = curl_getinfo($oCurl, CURLINFO_HEADER_SIZE);
				$header = substr($sContent, 0, $headerSize);
				$body = substr($sContent, $headerSize);
				curl_close($oCurl);
            return array('header'=>$header,'body'=>$body);
        }else{
			  curl_close($oCurl);
            return false;
        }
    }
	public function http_post_img($url,$param,$post_file=false){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		if (is_string($param) || $post_file) {
			$strPOST = $param;
		} else {
			$aPOST = array();
			foreach($param as $key=>$val){
			$aPOST[] = $key."=".urlencode($val);
			}
			$strPOST = join("&", $aPOST);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		if(intval($aStatus["http_code"])==200){
				$headerSize = curl_getinfo($oCurl, CURLINFO_HEADER_SIZE);
				$header = substr($sContent, 0, $headerSize);
				$body = substr($sContent, $headerSize);
				curl_close($oCurl);
            return array('header'=>$header,'body'=>$body);
		}else{
			return false;
		}
	}

	    /**
     * 微信api不支持中文转义的json结构
     * @param array $arr
     */
    public function wx_json_encode($arr) {
        $parts = array ();
        $is_list = false;
        $keys = array_keys ( $arr );
        $max_length = count ( $arr ) - 1;
        if (($keys [0] === 0) && ($keys [$max_length] === $max_length )) { //See if the first key is 0 and last key is length - 1
            $is_list = true;
            for($i = 0; $i < count ( $keys ); $i ++) { //See if each key correspondes to its position
                if ($i != $keys [$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }
        foreach ( $arr as $key => $value ) {
            if (is_array ( $value )) { //Custom handling for arrays
                if ($is_list)
                    $parts [] = $this->wx_json_encode( $value ); /* :RECURSION: */
                else
                    $parts [] = '"' . $key . '":' . $this->wx_json_encode( $value ); /* :RECURSION: */
            } else {
                $str = '';
                if (! $is_list)
                    $str = '"' . $key . '":';
                //Custom handling for multiple data types
                if (!is_string ( $value ) && is_numeric ( $value ) && $value<2000000000)
                    $str .= $value; //Numbers
                elseif ($value === false)
                    $str .= 'false'; //The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes ( $value ) . '"'; //All other things
                // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
                $parts [] = $str;
            }
        }
        $json = implode ( ',', $parts );
        if ($is_list)
            return '[' . $json . ']'; //Return numerical JSON
        return '{' . $json . '}'; //Return associative JSON
    }
}