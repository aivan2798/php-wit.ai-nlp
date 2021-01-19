<?php

//the purpose of this file is to extract out wit json data and transform into easily usable php data 😁

class EntityAlias
{
 $name;
 $roleS;
}

class IntentAlias
{
 $name;
}

class TraitAlias
{
 $name;
 $value;
}

class WitParse
{
 public $wit_entity;
 public $wit_intent;
 public $wit_trait;
 public $wit_confidence;
 public $wit_value;
 public $wit_role;
 
 public $WIT_ENTITY=0;
 public $WIT_INTENT=1;
 public $WIT_TRAIT=3;

 
 
 
 
 
 
 //parser initialisation fill wit objects
 public function __construct($witMsg)
 {
  $wit_nlp=$witMsg->entry[0]->messaging[0]->message->nlp;
  
 //$this->wit_entity=$wit_nlp->entities;
 $this->wit_intent=$wit_nlp->intents;
 $this->wit_trait=$wit_nlp->traits;
 $this->wit_entity=$wit_nlp->entities;
 }
 
 //wit object selector
 function witSwitch($active_wit,$wit_selector)
 {
  switch($wit_selector)
  {
   case "WIT_INTENT":
   $active_wit=$this->wit_intent;
  case "WIT_ENTITY":
  	$active_wit=$this->wit_entity;
  case "WIT_TRAIT":
  	$active_wit=$this->wit_trait;
  }
 }
 //end of wit selector
 
 //make confidence map of wit objects
 public function getConfidence($wit_arr_keyz,$wit_object_type)
 {
 $wit_active_object;
 $wit_conf_map=array();
 
 $this->witSwitch($wit_active_object,$wit_object_type);
  foreach($wit_arr_keyz as $keylist)
  {
   //get confidence of each object
   //backup $keylist[0]
   $conf=$wit_active_object->$keylist->confidence;
   $wit_conf_map[$keylist]=$conf;
  }
  
  arsort($wit_conf_map,SORT_NUMERIC);
  return $wit_conf_map;
 }
 //confidence mapper end
 
 //---Wit Intent parse
  public function parseIntent($wit_Earr)
 {
  $Iname=$this->wit_Intent->key($wit_Earr)->name;

 }
 //end of Intent parser

 
 
 
 //wit entity parser
 public function parseEntity($wit_Earr)
 {
  $Ename=$this->wit_entity->key($wit_Earr)->name;

$Erole=$this->wit_entity->key($wit_Earr)->role;

$Evalue=$this->wit_entity->key($wit_Earr)->value;
 }
 //end of entity parser

//trait parser
public function parseTrait($wit_Earr)
 {
  $Tname=$this->wit_trait->key($wit_Earr);
$Tvalue=$this->wit_entity->key($wit_Earr)->value;
 }

 //wit object parser
 public function wit_object_parser($wit_object_type)
 {
 $keyz=array();
 
 //wit object switcher
 switch($wit_object_type)
 {
  case "WIT_INTENTS":
  $wit_intent_confidence_rank=array();
  //return key $keylist
  //backup get_object_vars
  $keyz=array_keys($this->wit_intent);
  
  //get number of intents
 	 $intent_no=count($keyz);
 	 
 	 $wit_intent_confidence_rank=$this->get_confidence($keyz,$wit_object_type);
 	 $this->parseIntent($$wit_intent_confidence_rank);
 	
  case "WIT_ENTITY":
  $wit_entity_confidence_rank=array();
  $keyz=get_object_vars($this->wit_entity);
 	$intent_no=count($keyz);
 	 $wit_entity_confidence_rank=$this->get_confidence($keyz,$wit_object_type);
 	 
 	 $this->parseEntity($$wit_entity_confidence_rank);

  
  case "WIT_TRAITS":
  $wit_trait_confidence_rank=array();
 	$keyz=get_object_vars($this->wit_trait);
 	$trait_no=count($keyz);
 $wit_trait_confidence_rank=$this->get_confidence($keyz,$wit_object_type);
 	 $this->parseTrait($$wit_trait_confidence_rank);
 }
 	//end of switcher
 
 }
 //end of parser object fx
}




?>