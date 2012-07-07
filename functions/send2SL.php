<?php
	/*ex:
		send2SL("3efbcae5-7d1e-7698-7d7f-a62a4b0da785", "test", 1);
	*/
	
	function send2SL($channel, $string, $int)
	{
		$xml = "<?xml version=\"1.0\"?><methodCall><methodName>llRemoteData</methodName>
		<params><param><value><struct>
		<member><name>Channel</name><value><string>".$channel."</string></value></member>
		<member><name>IntValue</name><value><int>".$int."</int></value></member>
		<member><name>StringValue</name><value><string>".$string."</string></value></member>
		</struct></value></param></params></methodCall>";
		return sendToHost("xmlrpc.secondlife.com", "POST", "/cgi-bin/xmlrpc.cgi", $xml);
	}
 
	function sendToHost($host,$method,$path,$data,$useragent=0)
	{ 
		$buf="";
		// Supply a default method of GET if the one passed was empty 
		if (empty($method)) 
			$method = 'GET'; 
		$method = strtoupper($method); 
 
		$fp = fsockopen($host, 80, $errno, $errstr, 30);
 
		if( !$fp )
		{
			$buf = "$errstr ($errno)<br />\n";
		}else
		{
			if ($method == 'GET') 
			$path .= '?' . $data; 
			fputs($fp, "$method $path HTTP/1.1\r\n"); 
			fputs($fp, "Host: $host\r\n"); 
			fputs($fp, "Content-type: text/xml\r\n"); 
			fputs($fp, "Content-length: " . strlen($data) . "\r\n"); 
			if ($useragent) 
				fputs($fp, "User-Agent: MSIE\r\n"); 
			fputs($fp, "Connection: close\r\n\r\n"); 
			if ($method == 'POST') 
				fputs($fp, $data); 
			while (!feof($fp)) 
				$buf .= fgets($fp,128); 
			fclose($fp); 
		}
		
		$delim = array("Channel","StringValue","IntValue");
		$step = str_replace($delim, '+delim+', $buf);
		$bufarray = explode('+delim+', $step);
		
		$reply = strip_tags($bufarray[2]);
		if($reply == "{success}")
		{
			return true;
		}
		else if($reply == "{failure}")
		{
			return false;
		}
		else
		{
			return false;
		}
	} 
?>