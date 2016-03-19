<?php

/**
 * Plik większy, znacznik w nagłówku <zawartosc_pliku>calosc</zawartosc_pliku>
 * eksport pełny -- zawierający zawsze całą bazę biura nieruchomości jest
 * wysyłany raz na 30 dni lub na życzenie portalu, gdy trzeba nadpisać bazę
 * z jakiegoś powodu. Nadpisanie trzeba rozumieć dosłownie, czyli po
 * przetworzeniu tego pliku w portalu powinny znajdować się tylko oferty,
 * które są w tym pliku.
 */

define('ROOT_DIR', str_replace('/imo', '',dirname(__FILE__)));


//echo ROOT_DIR; exit();

include(ROOT_DIR.'/imo/classes/FilesChecker.php');
include(ROOT_DIR.'/imo/classes/FileReader.php');
include(ROOT_DIR.'/imo/classes/Offer.php');
include(ROOT_DIR.'/imo/classes/OfferMeta.php');
include(ROOT_DIR.'/imo/classes/Updater.php');
include(ROOT_DIR.'/imo/classes/Mysql.php');
include(ROOT_DIR.'/imo/classes/MysqlException.php');
include(ROOT_DIR.'/imo/classes/PrepareImage.php');
include(ROOT_DIR.'/imo/classes/Images.php');


$filesChecker = new FilesChecker();
//echo '<pre>';

while ($fileName = $filesChecker->getNewBigFile()) {

    $fileReader = new FileReader($fileName, FileReader::CALOSC);

    

    if ($xmlObiect = $fileReader->getXmlObiect()) {

        foreach($xmlObiect['lista_ofert'] as $listaOfert) {
            foreach ($listaOfert['dzial'] as $dzial) {
                $dzialAttr = $dzial['attr'];

                if(!isset($dzial['oferta'])) {
                    continue;
                }

                foreach ($dzial['oferta'] as $oferta) {
                    $offer = new Offer($oferta, $fileName, $dzialAttr);

                    if($offer->getIsOk()) {
                        echo "UP - ".$offer->getOfferId()."\n";
                        $updater = new Updater($offer);
                    }
                    else {
                        echo "SKIP - ".$offer->getOfferId()."\n";
                    }
                    echo '---------'."\n\n";
                }
                echo '-----------------------------------------------'."\n\n";
            }
        }
    }
}
