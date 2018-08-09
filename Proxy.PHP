<?php
$destinationURL = 'http://www.otherdomain.com/backend.php';
$RequestDomain = 'example.com';

if(!function_exists('apache_request_headers')) {
    function apache_request_headers() {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if(substr($key, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
            }
        }
        return $headers;
    }
}
$ip = '';
if (!empty($_SERVER['HTTP_CLIENT_IP'])) { 
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

$req_parts = parse_url($_SERVER['HTTP_REFERER']);

if($req_parts["host"] == $RequestDomain) {
    $method = $_SERVER['REQUEST_METHOD'];
	if ($method == "GET") {
		$data=$_GET;
	} elseif ($method=="POST" && count($_POST)>0) {
		$data=$_POST;
	} else {
		$data = $HTTP_RAW_POST_DATA;
	}
    $response = proxy_request($destinationURL, ($method == "GET" ? $_GET : $_POST), $method);
    $headerArray = explode("\r\n", $response['header']);
	$is_gzip = false;
	$is_chunked = false;
    foreach($headerArray as $headerLine) {
		if($headerLine == "Content-Encoding: gzip") {
			global $is_gzip;
			$is_gzip = true;
		} elseif($headerLine == "Transfer-Encoding: chunked") {
			global $is_chunked;
			$is_chunked = true;
		} else {
     $ip = $_SERVER['REMOTE_ADDR'];
		}
    }
	$contents = $response['content'];
	if($is_chunked) {
		$contents = decode_chunked($contents);
	}
	if($is_gzip) {
		$contents = gzdecode($contents);
	}
	echo $contents;
  } else {
    echo $domainName." Is Not An Authorized Domain...";
  }

  function proxy_request($url, $data, $method) {

	$req_dump = print_r($data, TRUE);

    global $ip;
	if ($method == "GET")  {
		$data = http_build_query($data);
		$data = $data . parse_url($url)["query"];
	} elseif ($method=="POST" && count($_POST)>0) {
		$data = http_build_query($data);
		$data = $data . parse_url($url)["query"];
	} else {
		$data = $data;
	}
    $datalength = strlen($data);
 
    $url = parse_url($url);
 
    if ($url['scheme'] != 'http') { 
        die('Error: Only HTTP Request Are Supported!');
    }

    $host = $url['host'];
    $path = $url['path'];
    
	if ($url['scheme'] == 'http') {
   		 $fp = fsockopen($host, 80, $errno, $errstr, 30);
    } elseif ($url['scheme'] == 'https') {
    	$fp = fsockopen($host, 443, $errno, $errstr, 30);
	}
 
    if ($fp){
        if($method == "POST") {
            fputs($fp, "POST $path HTTP/1.1\r\n");
        } else {
            fputs($fp, "GET $path?$data HTTP/1.1\r\n");
        }
        fputs($fp, "Host: $host\r\n");
        
        fputs($fp, "X-Forwarded-For: $ip\r\n");
        fputs($fp, "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n"); 
        
           $requestHeaders = apache_request_headers();
        while ((list($header, $value) = each($requestHeaders))) {
            if($header == "Content-Length") {
                fputs($fp, "Content-Length: $datalength\r\n");
            } else if($header !== "Connection" && $header !== "Host" && $header !== "Content-length") {
                fputs($fp, "$header: $value\r\n");
            }
        }
        fputs($fp, "Connection: Close\r\n\r\n");
        fputs($fp, $data);
 
        $result = ''; 
        while(!feof($fp)) {
            $result .= fgets($fp, 128);
        }
    }
    else { 
        return array(
            'status' => 'err', 
            'error' => "$errstr ($errno)"
        );
    }
 
    fclose($fp);

    $result = explode("\r\n\r\n", $result, 2);
    $header = isset($result[0]) ? $result[0] : '';
    $content = isset($result[1]) ? $result[1] : '';

    return array(
        'status' => 'ok',
        'header' => $header,
        'content' => $content
    );
}

function decode_chunked($str) {
  for ($res = ''; !empty($str); $str = trim($str)) {
    $pos = strpos($str, "\r\n");
    $len = hexdec(substr($str, 0, $pos));
    $res.= substr($str, $pos + 2, $len);
    $str = substr($str, $pos + 2 + $len);
  }
  return $res;
}

?>
