<?php
namespace Itumulak\WpSsoFirebase\Models;

class UserProfileWP_Model extends Base_Model {
    private string $handle;

    public function __construct()
    {
        $this->handle = 'wp_firebase_profile';
    }

    public function get_handle()
    {
        return $this->handle;
    }
}