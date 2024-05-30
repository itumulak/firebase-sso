<?php
namespace Itumulak\WpSsoFirebase\Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

abstract class Base_Controller {
    abstract public function init() : void;
    abstract public function scripts() : void;
}