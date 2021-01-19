<?php
//curl -X GET "localhost:19980/index.php?hub.verify_token=deadend&hub.challenge=CHALLENGE_ACCEPTED&hub.mode=subscribem"


//curl -H "Content-Type: application/json" -X POST "localhost:19980/index.php" -d '{"object": "page", "entry": [{"messaging": [{"message": "WELCOME HOME"}]}]}'


include"witParse.php";


$hook_msg_type=$_SERVER["REQUEST_METHOD"];

//$hook_msg;


class HookReply
{
 public $messaging_type;
 public $recipient=array("id"=>"");
 public $message=array("text"=>"");
}




class HookIO
{
  
 private $access_token;
 private $hook_mode;
 private $hook_challenge;
 private $hook_token;
 private $hook_type;
 private $nlpHandler;
 
 private $hook_send_apiUrl="https://graph.facebook.com/v8.0/me/messages?access_token=<PAGE_ACCESS_TOKEN>";
 
 
 public $hook_data;
 
 public function __construct($atoken="deadend")
  {
 		$this->access_token=$atoken;
  }
 
//hook verification code
function hookPass()
{
 $this->hook_mode=$_GET["hub_mode"];
 $this->hook_challenge=$_GET["hub_challenge"];
 $this->hook_token=$_GET["hub_verify_token"];

  
 echo($this->access_token);
 echo($this->hook_mode);
 echo("\n");
  
  
 		if(($this->hook_mode)&&($this->hook_token))
 		{
  if(($this->hook_token)==($this->access_token))
  	{
   echo($this->hook_challenge);
  	}
 		 else
 	 {
   echo("hook token failed");
 	 }
 }
 else
 {
 echo("hook request empty");
 }
}
//------end of verification-----


//hook data obtained
function hookIn()
{
	$hook_raw_data=file_get_contents("php://input");
	$hook_ripe_data=json_decode($hook_raw_data);
	/*print_r($hook_ripe_data["entry"]["messaging"]);*/


	//$this->hook_type=$this->hookType($hook_ripe_dta);

	$this->hook_data=$hook_ripe_data;
	echo($hook_ripe_data->object);
	//return $hook_ripe_data;
}
//-----end of hook data fx-----//


//nlp processor function
function nlpProcessor()
{
 $this->nlpHandler=new WitParse($this->hook_data);
  
}
//-----end of nlp processor fx


//quick reply processor
function quickReplyProcessor()
{


}
//------end of quick reply processor---


//hook type extractor
function hookType($hookD)
{
$global_hook=0;

$hook_msgin=$hookD->entry[0]->messaging[0];


$hook_msg_type=$hook_msgin->message;
 
 
 
 $hook_msg_types=get_object_vars($hook_msg_type);
 $hook_msgin_types=get_object_vars($hook_msgin);
 
 foreach($hook_msgin_types as $typem_name)
 {
 		switch($typem_name)
 		{
 		 case "pass_thread_control":
 		 $this->hook_type=$typem_name;
 		 $global_hook=1;
 		 break;
 		 case "take_thread_control":
 		 $this->hook_type=$typem_name;
 		 $global_hook=1;
 		 break;
 		 case "request_thread_control":
 		 $this->hook_type=$typem_name;
 		 $global_hook=1;
 		 break;
 		 case "app_roles":
 		 $this->hook_type=$typem_name;
 		 $global_hook=1;
 		 break;
 		 default:
 		 break;
 		}
 }
 //end of hook type iterator
 
 
 
 if($global_hook==0)
 {
 foreach($hook_msg_types as $type_name)
 {
 		switch($type_name)
 		{
 		 case "quick_reply":
 		 $this->hook_type=$type_name;
 		 break;
 		 case "nlp":
 		 $this->hook_type=$type_name;
 		 break;
 		}
 }
 }
}
//-----END OF HOOK TYPE FUNCTION----//








//Wit reply builder
function hookOut($HookReply)
{
$hook_reply_data=new HookReply;

$hookOut_hook_data=$this->hook_data;

$hook_reply_data->recipient["id"]=$hookOut_hook_data->entry[0]->messaging[0]->sender->id;
$hook_reply_data->message["text"]=$HookReply;


$hook_reply_msg=json_encode($hook_reply_data);

//hook post to send api
$curl_start=curl_init($hook_send_apiUrl);
 curl_setopt_array($curl_start,array(CURLOPT_RETURNTRANSFER=>true,CURLOPT_POSTFIELDS=>$hook_reply_msg,CURLOPT_HTTPHEADER=>array("Content-Type:application/json")));
 
 
 //curl_setopt($curl_start,CURLOPT_POSTFIELDS,$hook_reply_msg);

curl_exec($curl_start);
//finish
}
//end of reply function

//nlp processor





function msgHookEars()
{
 $msg_type=$GLOBALS["hook_msg_type"];
 switch($msg_type)
	{
		case "GET":
		$this->hookPass();
		break;
	
		case "POST":
 		$this->hookIn();
 		switch($this->hook_type)
 		{
 		
 		case "nlp":
 		$this->nlpProcessor();
 		break;
 		
 		case "quick_replies":
 		$this->quickReplyProcessor();
 		break;
 		}
//echo("\n");
//print_r($this->hook_data->entry[0]->messaging[0]->message);
echo("\n");
 		break;
  }


}
//hook class end

}


//$wit=new HookIO();


	
?>