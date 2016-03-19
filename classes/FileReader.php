<?php

class FileReader
{
    const CALOSC = 'calosc';
    const ROZNICA = 'roznica';

    private $fileName;
    private $xmlObiect;
    private $whichFile;

    public function __construct($fileName, $whichFile)
    {
        $this->fileName = $fileName;
        $this->whichFile = $whichFile;

        $this->unpackXml();
    }

    public function getXmlObiect()
    {   
        if( (string) $this->xmlObiect['header'][0]['zawartosc_pliku'][0]['values'] == $this->whichFile) {
            return $this->xmlObiect;
        }

        return false;
    }

    private function unpackXml()
    {
        $result = file_get_contents('zip://'.$this->fileName .'#oferty.xml');

        $xml = simplexml_load_string($result);
        $array = $this->xml2js($xml);
  

        $this->xmlObiect = $array['plik'][0];
    }

    function xml2js($xmlnode) {
        $root = (func_num_args() > 1 ? false : true);
        $jsnode = [];

        if (!$root) {
            if (count($xmlnode->attributes()) > 0){
                $jsnode["attr"] = [];
                foreach($xmlnode->attributes() as $key => $value)
                    $jsnode["attr"][$key] = (string)$value;
            }

            $textcontent = trim((string)$xmlnode);
            if (count($textcontent) > 0)
                $jsnode["values"] = $textcontent;

            foreach ($xmlnode->children() as $childxmlnode) {
                $childname = $childxmlnode->getName();
                if (!array_key_exists($childname, $jsnode))
                    $jsnode[$childname] = [];
                array_push($jsnode[$childname], $this->xml2js($childxmlnode, true));
            }
            return $jsnode;
        } else {
            $nodename = $xmlnode->getName();
            $jsnode[$nodename] = [];
            array_push($jsnode[$nodename], $this->xml2js($xmlnode, true));
            return $jsnode;
        }
    }   


    private function xmlToObiect($xml)
    {
        $this->xmlObiect = $xml;
    }
}
