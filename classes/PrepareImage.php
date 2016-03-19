<?php

class PrepareImage
{
    const UPLOAD_DIR = '/public_html/wpress/wp-content/uploads';

    private $imagesCollection = [];
    private $dateDir;
    private $image;



    public function __construct($imageName, $zipName)
    {
        $this->dateDir = 'oferty';

        $this->createDirectoryIfNotExist();

        $this->image = new Images();
        $this->image->post_date = date("Y-m-d H:i:s");
        $this->image->post_date_gmt = date("Y-m-d H:i:s");
        $this->image->guid = 'http://wpress.in-house.pl/wp-content/uploads/'.$this->dateDir.'/'.$imageName;

        $b = explode('.', $imageName, -1);

        $naameToBase = implode('.', $b);

        $this->image->post_title = $naameToBase;
        $this->image->post_name = Offer::slugify($naameToBase);

        $this->image->set_wp_attached_file($this->dateDir.'/'.$imageName);

        // a:5:{s:10:"image_meta";a:12:{s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"1";s:8:"keywords";a:0:{}}}

        $result = @file_get_contents('zip://'.$zipName.'#'.$imageName);

        if($result) {
            file_put_contents(ROOT_DIR.self::UPLOAD_DIR.'/'.$this->dateDir.'/'.$imageName, $result);

            list($width, $height, $type, $attr) = getimagesize(ROOT_DIR.self::UPLOAD_DIR.'/'.$this->dateDir.'/'.$imageName);

            $fileInfo = [
                'width' => $width,
                'height' => $height,
                'file' => $this->dateDir.'/'.$imageName,
                'sizes' => [
                   'thumbnail' => [
                        'file' => $this->dateDir.'/'.$imageName,
                        'width' => $width,
                        'height' => $height,
                        'mime-type' => 'image/jpeg'
                    ],
                    'medium' => [
                        'file' => $this->dateDir.'/'.$imageName,
                        'width' => $width,
                        'height' => $height,
                        'mime-type' => 'image/jpeg'
                    ],
                    'medium_large' => [
                        'file' => $this->dateDir.'/'.$imageName,
                        'width' => $width,
                        'height' => $height,
                        'mime-type' => 'image/jpeg'
                    ],
                    'large' => [
                        'file' => $this->dateDir.'/'.$imageName,
                        'width' => $width,
                        'height' => $height,
                        'mime-type' => 'image/jpeg'
                    ],
                    'thumbnail-1600' => [
                        'file' => $this->dateDir.'/'.$imageName,
                        'width' => $width,
                        'height' => $height,
                        'mime-type' => 'image/jpeg'
                    ],
                    'thumbnail-1200' => [
                        'file' => $this->dateDir.'/'.$imageName,
                        'width' => $width,
                        'height' => $height,
                        'mime-type' => 'image/jpeg'
                    ],
                    'thumbnail-16-9' => [
                        'file' => $this->dateDir.'/'.$imageName,
                        'width' => $width,
                        'height' => $height,
                        'mime-type' => 'image/jpeg'
                    ],
                    'thumbnail-1200-400' => [
                        'file' => $this->dateDir.'/'.$imageName,
                        'width' => $width,
                        'height' => $height,
                        'mime-type' => 'image/jpeg'
                    ],
                    'thumbnail-400-300' => [
                        'file' => $this->dateDir.'/'.$imageName,
                        'width' => $width,
                        'height' => $height,
                        'mime-type' => 'image/jpeg'
                    ],
                    'property-thumb' => [
                        'file' => $this->dateDir.'/'.$imageName,
                        'width' => $width,
                        'height' => $height,
                        'mime-type' => 'image/jpeg'
                    ],
                    'square-400' => [
                        'file' => $this->dateDir.'/'.$imageName,
                        'width' => $width,
                        'height' => $height,
                        'mime-type' => 'image/jpeg'
                    ],
                    'edit-screen-thumbnail' => [
                        'file' => $this->dateDir.'/'.$imageName,
                        'width' => $width,
                        'height' => $height,
                        'mime-type' => 'image/jpeg'
                    ],
                    'sow-carousel-default' => [
                        'file' => $this->dateDir.'/'.$imageName,
                        'width' => $width,
                        'height' => $height,
                        'mime-type' => 'image/jpeg'
                    ]
                ],
                'image_meta' => [
                    'aperture' => 0,
                    'credit' => '',
                    'camera' => '',
                    'caption' => '',
                    'created_timestamp' => '',
                    'copyright' => '',
                    'focal_length' => '0',
                    'iso' => '0',
                    'shutter_speed' => '0',
                    'title' => '',
                    'orientation' => '1',
                    'keywords' => [],
                ]
            ];

            $this->image->setFileInfo($fileInfo);
        }
    }

    public function getImage()
    {
        return $this->image;
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
        if(!file_exists(ROOT_DIR.self::UPLOAD_DIR.'/'.$this->dateDir)) {
            mkdir(ROOT_DIR.self::UPLOAD_DIR.'/'.$this->dateDir, 0777, 1);
        }
    }
}
