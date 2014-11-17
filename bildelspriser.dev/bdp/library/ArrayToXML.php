<?php
// from http://snipplr.com/view/3491/convert-php-array-to-xml-or-simple-xml-object-if-you-wish/
class ArrayToXML
{
    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     *
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */
    public static function toXml($data, $rootNodeName = 'data', &$xml=null)
    {
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1)
        {
            ini_set ('zend.ze1_compatibility_mode', 0);
        }

        if (is_null($xml))
        {
	    	$string="<$rootNodeName></$rootNodeName>";
            $xml = simplexml_load_string($string);
            
            //$xml = new SimpleXMLElement($sXML);
           // die($xml->asXML());
        }

        if(!is_array($data)){
        	// to handle non-array data
        	$data=array('scalar'=>$data);
        }
        
        
        // loop through the data passed in.
        foreach($data as $key => $value)
        {
            // no numeric keys in our xml please!
            if (is_numeric($key))
            {
                // make string key...
                $key = "unknownNode_". (string) $key;
            }
			// handle the key has one attribute appeded - key@attribute=value
            $ar_key = explode('@',$key);
            $attribute =""; $att_key = "";$att_value = "";$ar_atts=array();
            if(2==sizeof($ar_key)){
            	// if there actually was an attribute - set the values for it.
                $ar_atts = explode('=',$ar_key[1]);
                if(2==sizeof($ar_atts)){
                	$att_key = $ar_atts[0];
                	$att_value = $ar_atts[1];
                }
                $key=$ar_key[0];                	
            }
            // delete any char not allowed in XML element names
            $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

            // if there is another array found recrusively call this function
            if (is_array($value))
            {
            	$node = $xml->addChild($key);
            	// add attribute if set - add it to xml nodes.
            	if($att_key<>"") $node->addAttribute($att_key,$att_value);
                // recrusive call.
                ArrayToXML::toXml($value, $rootNodeName, $node);
            }
            else 
            {
                // add single node.
            	$value = htmlentities($value);
                $xml->addChild($key,$value);
            }

        }
        // pass back as string. or simple xml object if you want!
        return $xml->asXML();
    }
}
?>