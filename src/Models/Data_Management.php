<?php
namespace Itumulak\WpSsoFirebase\Models;

interface Data_Management {
	public function get( $key ) : string|bool|array;
	public function get_all(): array;
	public function save( array $data ) : bool;
}
