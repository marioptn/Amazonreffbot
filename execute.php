<?php
// recupero il contenuto inviato da Telegram
$content = file_get_contents("php://input");
// converto il contenuto da JSON ad array PHP
$update = json_decode($content, true);
// se la richiesta è null interrompo lo script
if(!$update)
{
  exit;
}
// assegno alle seguenti variabili il contenuto ricevuto da Telegram
$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$firstname = isset($message['chat']['first_name']) ? $message['chat']['first_name'] : "";
$lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['chat']['username']) ? $message['chat']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$text = isset($message['text']) ? $message['text'] : "";
// pulisco il messaggio ricevuto togliendo eventuali spazi prima e dopo il testo
$text = trim($text);
//$text = strtolower($text);
$array1 = array();

		
// gestisco la richiesta
$response = "";

if(isset($message['text']))
{
  $arr = explode("http", $text, 2);
  $testoLink = $arr[0];
  
  $dominioAmazon = get_string_between($text, "://www.", ".it");
	
  //NUOVO PARSER:
  //$text_url_array = parse_text($text);
  
  $text_url_array = getUrls($text);
	
  //$array1 = explode('.', $text_url_array[1]);
  //$dominio = $array1[1];
  //test url $string_test = var_export($array1, true);
	
  if(strpos($text, "/start") === 0 )
  {
	$response = "Ciao $firstname! \nMandami un link Amazon o condividilo direttamente con me da altre app! \nTi rispondero' con il link affiliato del mio padrone! Grazie mille!";
  }
  elseif($dominioAmazon == "amazon")
  {	  
	//new parser:
	$url_to_parse = $text_url_array[0];
	$url_affiliate = set_referral_URL($url_to_parse);
	$faccinasym = json_decode('"\uD83D\uDE0A"');
	$linksym =  json_decode('"\uD83D\uDD17"');
	$pollicesym =  json_decode('"\uD83D\uDC4D"');
	$worldsym = json_decode('"\uD83C\uDF0F"');
	$obj_desc = $testoLink;
	$short = make_bitly_url($url_affiliate,'ghir0','json');
	$response = "$obj_desc\n$worldsym $short";
	
   }
   elseif(strpos($text, "/link") === 0 && strlen($text)<6 )
  {
	   //$response = "Incolla l'URL Amazon da convertire dopo il comando /link";
   }
  else {
	  //$response = "$string_test";
  }
}
/*
*
* prende un link amazon, estrapola l'ASIN e ricrea un link allo stesso prodotto con il referral 
*/
function set_referral_URL($url){
	$referral = "arioptn-21";
	$url_edited = "";
	$parsed_url_array = parse_url($url);
	
	$seller = strstr($parsed_url_array['query'], 'm=');
	
	$parsed = extract_unit($fullstring, 'm=', '&');
	$seller = "&".$seller;
	$url_edited = "https://www.amazon.it".$parsed_url_array['path']."?tag=".$referral.$seller;
	return $url_edited;
}

header("Content-Type: application/json");
$parameters = array('chat_id' => $chatId, "text" => $response);
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
