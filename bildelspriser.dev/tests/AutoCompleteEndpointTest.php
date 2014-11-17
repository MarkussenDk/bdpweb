<?php

//include "../vendor/autoload.php";

//Remove UTF8 Bom

function remove_utf8_bom($text)
{
    $bom = pack('H*','EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);
    return $text;
}

class FrontPageTest extends PHPUnit_Framework_TestCase
{
    // ...

    public function testLoadAutoCompleteJson()
    {
        $domain = "http://bildelspriser.dev";
        $url = $domain."/index/autocompletecarmodels/car_make_id/62.json";
        // Arrange
        $json_response_text = remove_utf8_bom(file_get_contents($url));

        //echo "\nURL \n".$url;
        //echo "\nContent \n".$json_response_text;
        
        // Act
        $obj = json_decode($json_response_text);
        
        // Assert
        $json_start_pos = stripos($json_response_text, "{");
        $this->assertGreaterThan(0 , $json_start_pos ,"JSON Start bracket must be in the string (pos=$json_start_pos).");
        //$this->assertLessThan(5 , $json_start_pos ,"JSON Start bracket in beginning of the string (pos=$json_start_pos).");
        $this->assertStringStartsWith('{', $json_response_text);
        $this->assertStringEndsWith('}', $json_response_text);
        //$this->assertEquals(-1, $b->getAmount());
    }

    // ...
}
