<?php
class rcodehub_WC_rch_store_sale_basic {
	protected $rcodehub_actions;
	protected $rcodehub_filters;
	public function __construct() {
		$this->actions = array();
		$this->filters = array();
	}

	public function rcodehub_add_action( $rcodehub_hook, $rcodehub_component, $rcodehub_callback, $rcodehub_priority = 10, $rcodehub_accepted_args = 1 ) {
		$this->actions = $this->rcodehub_add( $this->actions, $rcodehub_hook, $rcodehub_component, $rcodehub_callback, $rcodehub_priority, $rcodehub_accepted_args );
	}

	public function rcodehub_add_filter( $rcodehub_hook, $rcodehub_component, $rcodehub_callback, $rcodehub_priority = 10, $rcodehub_accepted_args = 1 ) {
		$this->filters = $this->rcodehub_add( $this->filters, $rcodehub_hook, $rcodehub_component, $rcodehub_callback, $rcodehub_priority, $rcodehub_accepted_args );
	}

	private function rcodehub_add( $rcodehub_hooks, $rcodehub_hook, $rcodehub_component, $rcodehub_callback, $rcodehub_priority, $rcodehub_accepted_args ) {
		$rcodehub_hooks[] = array(
			'hook'          => $rcodehub_hook,
			'component'     => $rcodehub_component,
			'callback'      => $rcodehub_callback,
			'priority'      => $rcodehub_priority,
			'accepted_args' => $rcodehub_accepted_args,
		);
		return $rcodehub_hooks;
	}

	public function rcodehub_run() {
		foreach ( $this->filters as $rcodehub_hook ) {
			add_filter( $rcodehub_hook['hook'], array( $rcodehub_hook['component'], $rcodehub_hook['callback'] ), $rcodehub_hook['priority'], $rcodehub_hook['accepted_args'] );
		}
		foreach ( $this->actions as $rcodehub_hook ) {
			add_action( $rcodehub_hook['hook'], array( $rcodehub_hook['component'], $rcodehub_hook['callback'] ), $rcodehub_hook['priority'], $rcodehub_hook['accepted_args'] );
		}
	}
}
