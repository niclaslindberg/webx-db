<?php 
namespace Nillsoft\Db\Impl;

class Properties {
	
	
	/**
	 * 
	 * @param unknown $property the id of the property to find (maybe traversal in form of a.b.c
	 * @param unknown $array the array to search in
	 * @param unknown $required if instance of Exception thrown if missing otherwhise if evaluates to array it validates that the value in in the array other wise if to true throws NoPropException if missing.
	 * @param string $default
	 * @throws NoGetException
	 * @throws unknown
	 * @return Ambigous <NULL, unknown>|string
	 */
	public static function any($property, $array, $required=true, $default=null)
	{
		if ($val = isset($array[$property]) ? $array[$property] : NULL) {
			return $val;
		}
		if($default!==NULL) {
			return $default;
		}
		if ($required) {
			throw new PropertyException($property);
		}
	}
	
	public static function int($id, $array, $required=true, $default=0) {
		return intval(self::any($id,$array,$required,$default));	
	}
	
	public static function float($id, $array, $required=true, $default=0) {
		return floatval(self::any($id,$array,$required,$default));
	}

	public static function bool($id,$array, $required=true,$default=false) {
		return boolval(self::any($id,$array,$required,$default));
	}

	public static function string($id, $array, $required=true, $default=null) {
		return strval(self::any($id,$array,$required,$default));
	}
}
?>