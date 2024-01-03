<?php
namespace Itumulak\WpSsoFirebase\Models\Interface;

interface Data_Management_Interface {
	public function get( $key ) : string|bool|array;
	public function get_all(): array;
	public function save( array $data ) : bool;
}
