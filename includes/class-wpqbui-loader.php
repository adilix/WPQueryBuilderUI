<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and runs all plugin hooks.
 */
class WPQBUI_Loader {

	/** @var array */
	protected $actions = array();

	/** @var array */
	protected $filters = array();

	/**
	 * @param string $hook
	 * @param object $component
	 * @param string $callback
	 * @param int    $priority
	 * @param int    $accepted_args
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions[] = compact( 'hook', 'component', 'callback', 'priority', 'accepted_args' );
	}

	/**
	 * @param string $hook
	 * @param object $component
	 * @param string $callback
	 * @param int    $priority
	 * @param int    $accepted_args
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters[] = compact( 'hook', 'component', 'callback', 'priority', 'accepted_args' );
	}

	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
	}
}
