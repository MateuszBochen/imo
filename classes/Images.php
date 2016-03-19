<?php

class Images
{
    //public $id;
    public $post_author = 1;
    public $post_date;
    public $post_date_gmt;
    public $post_content;
    public $post_title = 'IF';
    public $post_excerpt = 'G';
    public $post_status = 'inherit';
    public $comment_status = 'open';
    public $ping_status = 'closed';
    public $post_password;
    public $post_name = 'if';
    public $to_ping;
    public $pinged;
    public $post_modified;
    public $post_modified_gmt;
    public $post_content_filtered;
    public $post_parent;
    public $guid;
    public $menu_order;
    public $post_type = 'attachment';
    public $post_mime_type = 'image/jpeg';
    public $comment_count;

    private $_wp_attachment_metadata = [];
    private $_wp_attached_file;

    public function setFileInfo($fileInfo)
    {
        $this->_wp_attachment_metadata = $fileInfo;
    }

    public function getFileInfo()
    {
        return $this->_wp_attachment_metadata;
    }

    public function set_wp_attached_file($_wp_attached_file)
    {
        $this->_wp_attached_file = $_wp_attached_file;
    }

    public function get_wp_attached_file()
    {
        return $this->_wp_attached_file;
    }
}
