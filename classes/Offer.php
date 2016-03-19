<?php

class Offer
{
    private $offerMeta;

    // all public properties is are columns names
    public $id;
    public $post_author = 1;
    public $post_date;
    public $post_date_gmt;
    public $post_content;
    public $post_title;
    public $post_excerpt;
    public $post_status = 'publish';
    public $comment_status = 'open';
    public $ping_status = 'closed';
    public $post_password;
    public $post_name;
    public $to_ping;
    public $pinged;
    public $post_modified;
    public $post_modified_gmt;
    public $post_content_filtered;
    public $post_parent;
    public $guid;
    public $menu_order;
    public $post_type = 'property';
    public $post_mime_type;
    public $comment_count;

    private $n_geo_y;
    private $n_geo_x;
    private $ulica;
    private $imagesCollection = [];

    private $isOk = false;
    private $zipName = false;

    public function __construct($xmlDataOffer, $zipName, $dzialAttr)
    {
        $this->zipName = $zipName;

        $this->offerMeta = new OfferMeta();

        $this->setDzial($dzialAttr['typ']);
        $this->setTypNieruchomosci($dzialAttr['tab']);

        if (!(isset($xmlDataOffer['id'][0]['values']) && $xmlDataOffer['id'][0]['values'] > 0)) {
            return false;
        }

        $this->isOk = true;

        $this->offerMeta->estate_nr_oferty = $xmlDataOffer['id'][0]['values'];
        $this->post_title = "Oferta nr ".$this->offerMeta->estate_nr_oferty;
        $this->post_name = $this->slugify($this->post_title);

        foreach ($xmlDataOffer['param'] as $param) {

           $methodName = 'set'.ucfirst($param['attr']['nazwa']);

            if(method_exists($this, $methodName)) {
                if($methodName == 'setOpis') {
                    $this->$methodName($param['linia']);
                }
                else {
                    $this->$methodName($param['values']);
                }
            }
        }

        $this->setPrice($xmlDataOffer['cena'][0]['values'], $xmlDataOffer['cena'][0]['attr']['waluta']);

        if(isset($xmlDataOffer['location'])) {
            $this->setLocation($xmlDataOffer['location'][0]['area']);
        }

        //echo "-----------------------------------------------------------------------------\n\n";
    }

    public function getIsOk()
    {
        return $this->isOk;
    }

    public function setDzial($text)
    {
        $a = ['sprzedaz' => 10, 'wynajem' => 9];

        if(isset($a[$text])) {
            $this->offerMeta->acf___property___status = $a[$text];
        }

        return $this;
    }

    public function setTypNieruchomosci($type)
    {
        $a = ['mieszkania' => 14, 'wynajem' => 9];

        if(isset($a[$type])) {
            $this->offerMeta->acf___property___status = $a[$type];
        }

        $this->offerMeta->acf___property___type = $type;
    }

    public function setPrice($price, $prefix)
    {
        $this->offerMeta->estate_property_cena = $price;
        $this->offerMeta->estate_property_price_prefix = $prefix;
    }

    public function setOpis($lines)
    {
        $str = '';

        foreach ($lines as $line) {
            $str .= '<p>'.$line['values'].'</p>';
        }

        $this->post_content = $str;

        return $this;
    }


    public function setLocation($location)
    {
        $a = [];

        foreach($location as $area) {
            $a[] = $area['values'];
        }

        $a = array_reverse($a);
        $a = implode(', ', $a);

        //a:3:{s:7:"address";s:34:"5 Walbrook, London, United Kingdom";s:3:"lat";s:10:"51.5122249";s:3:"lng";s:20:"-0.09045309999999063";}

        $this->offerMeta->setEstate_property_google_maps('address', $a);
    }

    public function setN_geo_y($n_geo_y)
    {
        $this->n_geo_y = $n_geo_y;

        $this->offerMeta->setEstate_property_google_maps('lat', $n_geo_y);

        return $this;
    }

    public function setN_geo_x($n_geo_x)
    {
        $this->n_geo_x = $n_geo_x;

        $this->offerMeta->setEstate_property_google_maps('lng', $n_geo_x);

        return $this;
    }

    public function setUlica($ulica)
    {
        $this->ulica = $ulica;

        return $this;
    }

    public function setDataaktualizacji($dataaktualizacji)
    {
        $this->post_modified = $dataaktualizacji;
        $this->post_modified_gmt = $dataaktualizacji;

        return $this;
    }   

    public function setSuperoferta($superoferta)
    {
        $this->offerMeta->estate_property_featured = $superoferta;

        return $this;
    }

    public function setPowierzchnia($size)
    {
        $this->offerMeta->estate_property_size = $size;

        return $this;
    }

    public function setDatawprowadzenia($datawprowadzenia)
    {
        $this->post_date = $datawprowadzenia;
        $this->post_date_gmt = $datawprowadzenia;

        return $this;
    }

    public function setLiczbapokoi($liczbapokoi)
    {

        $this->offerMeta->estate_property_rooms = $liczbapokoi;

        return $this;
    }

    //liczbapieter
    public function setOgrzewanie($ogrzewanie)
    {
        switch ($ogrzewanie) {
            case 'kolektor s³oneczny':
                $this->offerMeta->acf___property___features[] = 36;
                break;
            case 'elektryczne':
                $this->offerMeta->acf___property___features[] = 24;
                break;
            case 'centralne':
                $this->offerMeta->acf___property___features[] = 3;
                break;
            case 'gazowe':
                $this->offerMeta->acf___property___features[] = 13;
                break;
        }

        return $this;
    }   

    public function setPiwnica($piwnica)
    {
        $this->offerMeta->acf___property___features[] = 20;

        return $this;
    }

    public function setPrand($prand)
    {
        $this->offerMeta->acf___property___features[] = 37;

        return $this;
    }

    public function setSila($sila)
    {
        $this->offerMeta->acf___property___features[] = 38;

        return $this;
    }

    public function setAgent_nazwisko($agent)
    {
        $this->offerMeta->estate_property_custom_agent['nazwisko'] = $agent;

        return $this;
    }

    public function setAgent_tel_kom($tel)
    {
        $this->offerMeta->estate_property_custom_agent['mobile_phone_number'] = $tel;

        return $this;
    }

    public function setAgent_tel_biuro($tel)
    {
        $this->offerMeta->estate_property_custom_agent['agent_tel_biuro'] = $tel;

        return $this;
    }

    public function setAgent_email($email)
    {
        $this->offerMeta->estate_property_custom_agent['email'] = $email;

        return $this;
    }

    public function getAgent()
    {
        return $this->offerMeta->estate_property_custom_agent;
    }

    public function setAgent($agentId)
    {
        $this->offerMeta->estate_property_custom_agent = $agentId;

        return $this;
    }

    private function addImageToCollection($name)
    {
        if(!$name){
            return false;
        }

        $pi = new PrepareImage($name, $this->zipName);

        $this->imagesCollection[] = $pi->getImage();

        $new = new Images();
    }
    public function setZdjecie1($name)
    {
        $this->addImageToCollection($name);
    }

    public function setZdjecie2($name)
    {
        $this->addImageToCollection($name);
    }
    public function setZdjecie3($name)
    {
        $this->addImageToCollection($name);
    }
    public function setZdjecie4($name)
    {
        $this->addImageToCollection($name);
    }
    public function setZdjecie5($name)
    {
        $this->addImageToCollection($name);
    }
    public function setZdjecie6($name)
    {
        $this->addImageToCollection($name);
    }
    public function setZdjecie7($name)
    {
        $this->addImageToCollection($name);
    }
    public function setZdjecie8($name)
    {
        $this->addImageToCollection($name);
    }
    public function setZdjecie9($name)
    {
        $this->addImageToCollection($name);
    }
    public function setZdjecie10($name)
    {
        $this->addImageToCollection($name);
    }
    public function setZdjecie11($name)
    {
        $this->addImageToCollection($name);
    }
    public function setZdjecie12($name)
    {
        $this->addImageToCollection($name);
    }
    public function setZdjecie13($name)
    {
        $this->addImageToCollection($name);
    }
    public function setZdjecie14($name)
    {
        $this->addImageToCollection($name);
    }
    public function setZdjecie15($name)
    {
        $this->addImageToCollection($name);
    }

    public function getOfferMeta()
    {
        return $this->offerMeta;
    }

    public function setImagesCollection($collection)
    {
        $this->imagesCollection = $collection;

        return $this;
    }

    public function getImagesCollection()
    {
        return $this->imagesCollection;
    }

    public function getOfferId()
    {
        return $this->offerMeta->estate_nr_oferty;
    }

    static public function slugify($text)
    {
      // replace non letter or digits by -
      $text = preg_replace('~[^\pL\d]+~u', '-', $text);

      // transliterate
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

      // remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);

      // trim
      $text = trim($text, '-');

      // remove duplicate -
      $text = preg_replace('~-+~', '-', $text);

      // lowercase
      $text = strtolower($text);

      if (empty($text))
      {
        return 'n-a';
      }

      return $text;
    }

    //a:3:{s:7:"address";s:34:"5 Walbrook, London, United Kingdom";s:3:"lat";s:10:"51.5122249";s:3:"lng";s:20:"-0.09045309999999063";}
}
