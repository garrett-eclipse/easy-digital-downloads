<?php
/**
 * Reports API - Reports Registry
 *
 * @package     EDD
 * @subpackage  Admin/Reports
 * @copyright   Copyright (c) 2018, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */
namespace EDD\Admin\Reports;

use EDD\Utils;

/**
 * Implements a singleton registry for registering reports.
 *
 * @since 3.0
 *
 * @see \EDD\Utils\Registry
 * @see \EDD\Utils\Static_Registry
 *
 * @method array get_report( string $report_id )
 * @method void  remove_report( string $report_id )
 */
class Registry extends Utils\Registry implements Utils\Static_Registry {

	/**
	 * Item error label.
	 *
	 * @since 3.0
	 * @var   string
	 */
	public $item_error_label = 'report';

	/**
	 * The one true Reports registry instance.
	 *
	 * @since 3.0
	 * @var   \EDD\Admin\Reports\Registry
	 */
	private static $instance;

	/**
	 * Retrieves the one true Reports registry instance.
	 *
	 * @since 3.0
	 *
	 * @return \EDD\Admin\Reports\Registry Reports registry instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Registry();
		}

		return self::$instance;
	}

	/**
	 * Handles magic method calls for report manipulation.
	 *
	 * @since 3.0
	 *
	 * @param string $name      Method name.
	 * @param array  $arguments Method arguments (if any)
	 * @return mixed Results of the method call (if any).
	 */
	public function __call( $name, $arguments ) {

		$report_id = isset( $arguments[0] ) ? $arguments[0] : '';

		switch( $name ) {
			case 'get_report':
				return parent::get_item( $report_id );
				break;

			case 'remove_report':
				parent::remove_item( $report_id );
				break;
		}
	}

	/**
	 * Adds a new report to the master registry.
	 *
	 * @since 3.0
	 *
	 * @param string $report_id   Report ID.
	 * @param array  $attributes {
	 *     Attributes of the report.
	 *
	 *     @type string $label    Report label.
	 *     @type int    $priority Priority by which to register the report.
	 *     @type array  $filters  Registered filters to expose for the report.
	 *     @type string $graph    Class to instantiate for building the graph.
	 * }
	 * @return bool True if the report was successfully registered, otherwise false.
	 */
	public function add_report( $report_id, $attributes ) {
		return parent::add_item( $report_id, $attributes );
	}

	/**
	 * Retrieves all registered reports with a given sorting scheme.
	 *
	 * @since 3.0
	 *
	 * @param string $sort Optional. How to sort the list of registered reports before retrieval.
	 *                     Accepts 'priority' or 'ID' (alphabetized by report ID), or empty (none).
	 *                     Default empty.
	 */
	public function get_reports( $sort = '' ) {
		// If sorting, handle it before retrieval from the ArrayObject.
		switch( $sort ) {
			case 'ID':
				parent::ksort();
				break;

			case 'priority':
				parent::uasort( function( $a, $b ) {
					if ( $a['priority'] == $b['priority'] ) {
						return 0;
					}

					return ( $a['priority'] < $b['priority'] ) ? -1 : 1;
				} );
				break;

			default: break;
		}

		return parent::get_items();
	}

}