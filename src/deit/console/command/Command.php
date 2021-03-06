<?php

namespace deit\console\command;
use deit\console\Event;
use deit\console\Application;
use \deit\event\EventManager;
use \deit\event\EventListenerInterface;
use deit\console\definition\Definition;

/**
 * Console command
 * @author James Newell <james@digitaledgeit.com.au>
 */
abstract class Command implements CommandInterface, EventListenerInterface {

	/**
	 * The console event
	 * @var     Event
	 */
	private $event;

	/**
	 * The definition
	 * @var     Definition
	 */
	private $definition;

	/**
	 * Constructs the command
	 */
	public function __construct() {
		$this->definition = new Definition();
		$this->configure();
	}

	/**
	 * Gets the event
	 * @return  Event
	 */
	public function getEvent() {
		return $this->event;
	}

	/**
	 * Sets the event
	 * @param   Event   $event
	 * @return $this
	 */
	public function setEvent(Event $event) {
		$this->event = $event;
		return $this;
	}

	/**
	 * Gets the console definition
	 * @return  Definition
	 */
	public function getDefinition() {
		return $this->definition;
	}

	/**
	 * Configures the command
	 */
	public function configure() {
	}

	/**
	 * Executes the command
	 */
	abstract public function execute();

	/**
	 * @inheritdoc
	 */
	public function attach(EventManager $em) {
		$em->attach(Application::EVENT_INTERRUPT, array($this, 'interrupt'));
	}

	/**
	 * @inheritdoc
	 */
	public function detach(EventManager $em) {
		$em->detach(Application::EVENT_INTERRUPT, array($this, 'interrupt'));
	}

	/**
	 * Handles the dispatch event
	 * @param   Event $event
	 */
	public function dispatch(Event $event) {

		//set the event
		$this->setEvent($event);

		//validate the console options
		$this->getDefinition()->validate($event);

		//execute the console command
		return $this->execute();
	}

	/**
	 * Handles the interrupt event
	 */
	public function interrupt() {
	}

} 