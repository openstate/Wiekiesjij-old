<?php

//$parse = xml_parser_create(); 

function dom_to_array($domnode, &$array) {
  $parent=$domnode;
  $domnode = $domnode->firstChild;
  $myname=$domnode->nodeName;
  $x=1;
  while (!is_null($domnode)) {
     switch ($domnode->nodeType) {
        
       case XML_ELEMENT_NODE: {
          
             if ( !$domnode->hasChildNodes()) {
                 $array[$domnode->nodeName]='';
             } else if ( $domnode->hasChildNodes() && $domnode->firstChild->nodeType==XML_TEXT_NODE) {
                 $array[$domnode->nodeName]=$domnode->firstChild->nodeValue;
             } else if ( $domnode->hasChildNodes() )  {
                
         $array_ptr = & $array[$domnode->nodeName];
           dom_to_array($domnode, $array_ptr);
         break;
       }
   }
   }
         $domnode = $domnode->nextSibling;
       if($domnode->nodeName==$myname)
       {
             $domnode->nodeName.=($x++);     
       }
  }
}

$file = "groningen.xml";
$showfile = file_get_contents($file);
if(!$domDocument = domxml_open_mem($newstring)) {
	echo "Couldn't load xml...";
    exit;
}

$rootDomNode = $domDocument->document_element();
$a = array();
dom_to_array($rootDomNode,$a);
print_r($a);

?>
