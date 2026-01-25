<?php

namespace SudoWP_PostGallery\Lib;

/**
 * Register all actions and filters for the plugin.
 *
 * @link       https://sudowp.com
 * @since      1.0.0
 * @package    SudoWP_PostGallery
 * @subpackage SudoWP_PostGallery/lib
 * @author     SudoWP <security@sudowp.com>
 */

// SudoWP Modernization: Enforce Strict Types
declare(strict_types=1);

class PostGalleryLoader {

	/**
	 * The array of actions registered with WordPress.
	 */
	protected array $actions;

	/**
	 * The array of filters registered with WordPress.
	 */
	protected array $filters;

	public function __construct() {
		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 */
	public function add_action( string $hook, object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 */
	public function add_filter( string $hook, object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 */
	private function add( array $hooks, string $hook, object $component, string $callback, int $priority, int $accepted_args ): array {
		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);

		return $hooks;
	}

	/**
	 * Register the filters and actions with WordPress.
	 */
	public function run(): void {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
	}
}