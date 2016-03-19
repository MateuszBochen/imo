<?php

class PrepareImages
{
    const UPLOAD_DIR = '/uploads/jeabnywordpress';

    private $imagesCollection = [];
    private $dateDir;

    public function __construct($xmlImages, $zipName)
    {
        $this->dateDir = date("Y/d");

        $this->createDirectoryIfNotExist();

        foreach($xmlImages->zdjecie as $imageXml) {
                
            $imageName = (string)$imageXml->nazwa;

            $action = (string)$imageXml->akcja;

            if($action == 'u') {
                //unlink(filename)
                continue;
            }

            $image = new Images();
            $image->post_date = date("Y-m-d H:i:s");
            $image->post_date_gmt = date("Y-m-d H:i:s");
            $image->guid = 'http://wpress.in-house.pl/wp-content'.self::UPLOAD_DIR.'/oferty/'.$imageName;

            $this->imagesCollection[(string)$imageXml->id][(string)$imageXml->kolejnosc] = $image;

            $result = @file_get_contents('zip://'.$zipName.'#'.$imageName);

            if($result) {
                file_put_contents(ROOT_DIR.self::UPLOAD_DIR.'/oferty/'.$imageName, $result);
            }

        }
    }


    public function getImagesCollection($offerId)
    {
        if(!isset($this->imagesCollection[$offerId])) {
            return [];
        }

        $collection = $this->imagesCollection[$offerId];

        ksort($collection);

        return $collection;
    }

    private function createDirectoryIfNotExist()
    {
        if(!file_exists(ROOT_DIR.self::UPLOAD_DIR.'/oferty')) {
            mkdir(ROOT_DIR.self::UPLOAD_DIR.'/oferty', 0777, 1);
        }
    }
}
