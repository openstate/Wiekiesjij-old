<?php
$p =& new xmlParser();
$p->parse($regionxml);

$xmlparty = array();
$xmlstel = array();
$party="";
$stel="";

// parse parties
foreach ($p->output as $l1k=>$l1v) {
	if ($l1v['name']=="SW") {
		// branch SW
		foreach ($l1v['child'] as $l2k=>$l2v) {
			if ($l2v['name']=="PARTIJ") {
				// branch party
				$party = $l2v['attrs']['ID'];
				foreach ($l2v['child'] as $l3k=>$l3v) {
					$xmlparty[$party][$l3v['name']]=$l3v['content'];
				}
			}
			if ($l2v['name']=="STELLING") {
				// branch stelling
				$stel = $l2v['attrs']['ID'];
				foreach ($l2v['child'] as $l3k=>$l3v) {
					// branch stelling properties
					$xmlstel[$stel][$l3v['name']]=$l3v['content'];
				}
			}
		}
	}
	
}

//echo "<pre>";
//print_r($xmlparty);
//print_r($xmlstel);
//echo "</pre>";

class xmlParser{
   var $xml_obj = null;
   var $output = array();
   var $attrs;

   function xmlParser(){
       $this->xml_obj = xml_parser_create();
       xml_set_object($this->xml_obj,$this);
       xml_set_character_data_handler($this->xml_obj, 'dataHandler');
       xml_set_element_handler($this->xml_obj, "startHandler", "endHandler");
   }

   function parse($path){
       if (!($fp = fopen($path, "r"))) {
           die("Cannot open XML data file: $path");
           return false;
       }

       while ($data = fread($fp, 4096)) {
           if (!xml_parse($this->xml_obj, $data, feof($fp))) {
               die(sprintf("XML error: %s at line %d",
               xml_error_string(xml_get_error_code($this->xml_obj)),
               xml_get_current_line_number($this->xml_obj)));
               xml_parser_free($this->xml_obj);
           }
       }

       return true;
   }

   function startHandler($parser, $name, $attribs){
       $_content = array();
       $_content['name'] = $name;
       if(!empty($attribs))
           $_content['attrs'] = $attribs;
       array_push($this->output, $_content);
}

   function dataHandler($parser, $data){
       if(!empty($data) && $data!="\n") {
           $_output_idx = count($this->output) - 1;
           $this->output[$_output_idx]['content'] .= $data;
       }
   }

   function endHandler($parser, $name){
       if(count($this->output) > 1) {
           $_data = array_pop($this->output);
           $_output_idx = count($this->output) - 1;
           $add = array();
           if(!$this->output[$_output_idx]['child'])
               $this->output[$_output_idx]['child'] = array();
           array_push($this->output[$_output_idx]['child'], $_data);
       } 
   }
}
?>
