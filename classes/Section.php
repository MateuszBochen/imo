<?php

class Section
{
    private $offersCollection = [];

    public function addOffer(Offer $offer)
    {
        $this->offersCollection[] = $offer;

        return $this;
    }

    public function getOffers()
    {
        return $this->offersCollection;
    }
}
