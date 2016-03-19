<?php

class Updater
{
    private $noAutoUpdate = ['estate_property_custom_agent'];

    private $offer;
    private $mysql;

    public function __construct(Offer $offer)
    {
        
        $this->mysql = new Mysql([
            
        ]);

        $this->offer = $offer;

        $this->updateAgent();
        $this->addOffer();
    }

    public function updateAgent()
    {
        $this->getAgentId();
    }

    public function addOffer()
    {
        //var_dump($this->offer);

        $reflect = new \ReflectionClass($this->offer);
        $props   = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);

        $dataArray = [];

        foreach($props as $prop) {
            $propName = (string)$prop->name;
            $dataArray[$propName] = $this->offer->$propName;
        }

        // $dataArray this array is are ready to inset 

        $this->mysql->insert('posts', $dataArray);
        $lastId = $this->mysql->lastId();

        $this->addOdfferMeta($lastId);

        $galleryImages = $this->addImages($lastId);
        $this->mysql->update('postmeta', ['meta_value' => serialize($galleryImages)], ['meta_key' => 'estate_property_gallery', 'post_id' => $lastId]);
        $this->mysql->update('postmeta', ['meta_value' => $galleryImages[0]], ['meta_key' => '_thumbnail_id', 'post_id' => $lastId]);
        
    }

    private function addOdfferMeta($offerId)
    {
        $offerMeta = $this->offer->getOfferMeta();

        $reflect = new \ReflectionClass($offerMeta);
        $props   = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);

        $dataArray = [];

        foreach($props as $prop) {

            $propName = (string)$prop->name;
            $metaKey = str_replace('___', '-', $propName);

            $dataArray[] = [
                'post_id' => $offerId,
                'meta_key' => $metaKey,
                'meta_value' => (is_array($offerMeta->$propName) ? serialize($offerMeta->$propName) : $offerMeta->$propName),
            ];
        }

        $this->mysql->insert('postmeta', $dataArray);
        $lastId = $this->mysql->lastId();
    }

    private function getAgentId()
    {
        $agent = $this->offer->getAgent();        

        $this->mysql->query("SELECT `id` FROM `wp_users` WHERE `user_email` = '".$agent['email']."' LIMIT 1");
        $agentId = $this->mysql->get();

        if(empty($agentId)) {
            $this->offer->setAgent($this->addNewAgent());
        }
        else {
            $this->offer->setAgent($agentId[0]['id']);
        }
    }

    private function addImages($offerId)
    {
        $images = $this->offer->getImagesCollection();
        
        $imagesId = [];

        foreach ($images as $image) {

            $image->post_parent = $offerId;

            $reflect = new \ReflectionClass($image);
            $props   = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);

            $dataArray = [];

            foreach($props as $prop) {
                $propName = (string)$prop->name;
                $dataArray[$propName] = $image->$propName;
            }

            $this->mysql->insert('posts', $dataArray);
            $lastId = $this->mysql->lastId();

            $metaArray[] = [
                'post_id' => $lastId,
                'meta_key' => '_wp_attached_file',
                'meta_value' => $image->get_wp_attached_file(),
            ];

            $metaArray[] = [
                'post_id' => $lastId,
                'meta_key' => '_wp_attachment_metadata',
                'meta_value' => addslashes(serialize($image->getFileInfo())),
            ];
            $this->mysql->insert('postmeta', $metaArray);

            $imagesId[] = $lastId;
        }

        return $imagesId;
    }

    private function addNewAgent()
    {
        $agent = $this->offer->getAgent();

        $data = [
            'user_login' => $agent['nazwisko'],
            'user_pass' => '$P$BMmTzkuSBn7fM/pQtQ9EhrddyxDBug/',
            'user_nicename' => $agent['nazwisko'],
            'user_email' => $agent['email'],
            'user_registered' => date("Y-m-d H:i:s"),
            'user_status' => 0,
            'display_name' => $agent['nazwisko'],
        ];

        $this->mysql->insert('users', $data);
        $lastId = $this->mysql->lastId();

        $names = explode(' ', $agent['nazwisko']);

        $dataMeta = [
            [
                'user_id' => $lastId,
                'meta_key' => 'nickname',
                'meta_value' => $agent['nazwisko']
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'first_name',
                'meta_value' => $names[0]
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'rich_editing',
                'meta_value' => 1
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'comment_shortcuts',
                'meta_value' => 0
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'admin_color',
                'meta_value' => 'fresh'
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'use_ssl',
                'meta_value' => '0'
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'wp_capabilities',
                'meta_value' => 'a:1:{s:5:"agent";b:1;}'
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'wp_user_level',
                'meta_value' => '1'
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'dismissed_wp_pointers',
                'meta_value' => ''
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'user_image',
                'meta_value' => ''
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'office_phone_number',
                'meta_value' => $agent['agent_tel_biuro']
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'mobile_phone_number',
                'meta_value' => $agent['mobile_phone_number']
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'fax_number',
                'meta_value' => ''
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'custom_facebook',
                'meta_value' => ''
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'custom_twitter',
                'meta_value' => ''
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'custom_google',
                'meta_value' => ''
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'custom_linkedin',
                'meta_value' => ''
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'subscribed_package',
                'meta_value' => ''
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'subscribed_package_default_id',
                'meta_value' => ''
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'user_package_activation_time',
                'meta_value' => ''
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'subscribed_listing_remaining',
                'meta_value' => ''
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'subscribed_featured_listing_remaining',
                'meta_value' => ''
            ],
            [
                'user_id' => $lastId,
                'meta_key' => 'subscribed_free_package_once',
                'meta_value' => ''
            ]
        ];

        if(isset($names[1])) {
            $dataMeta[] = [
                'user_id' => $lastId,
                'meta_key' => 'last_name',
                'meta_value' => $names[1]
            ];
        }

        $this->mysql->insert('usermeta', $dataMeta);

        return $lastId;
    }

}
