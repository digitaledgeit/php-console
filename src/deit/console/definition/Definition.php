<?php

namespace deit\console\definition;
use deit\console\Event;

/**
 * Console definition
 * @author James Newell <james@digitaledgeit.com.au>
 */
class Definition {

	/**
	 * The command name
	 * @var     string
	 */
	private $name;

	/**
	 * The command description
	 * @var     string
	 */
	private $description;

	/**
	 * The option definitions
	 * @var     Option[string]
	 */
	private $options = array();

	/**
	 * The argument definitions
	 * @var     Argument[string]
	 */
	private $arguments = array();

	/**
	 * The command version
	 * @var     string
	 */
	private $version;

	/**
	 * Gets the command name
	 * @return  string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the command name
	 * @param   string  $name
	 * @return  $this
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * Gets the command description
	 * @return  string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the command description
	 * @param   string  $description
	 * @return  $this
	 */
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}

	/**
	 * Gets the options
	 * @return  Option[]
	 */
	public function getOptions() {
		return array_values($this->options);
	}

	/**
	 * Gets whether the option is defined
	 * @param   string $name
	 * @return  bool
	 */
	public function hasOption($name) { //TODO: update for long and short name
		throw new \RuntimeException();
		return isset($this->options[$name]);
	}

	/**
	 * Gets an option from the short name
	 * @param   string  $name
	 * @return  Option|null
	 */
	public function getOptionByShortName($name) {
		foreach ($this->options as $option) {
			if ($option->getShortName() == $name) {
				return $option;
			}
		}
		return null;
	}

	/**
	 * Gets an option from the long name
	 * @param   string  $name
	 * @return  Option|null
	 */
	public function getOptionByLongName($name) {
		foreach ($this->options as $option) {
			if ($option->getLongName() == $name) {
				return $option;
			}
		}
		return null;
	}

	/**
	 * Adds an option
	 * @param   Option $option
	 * @return  $this
	 */
	public function addOption(Option $option) {
		$this->options[] = $option;
		return $this;
	}

	/**
	 * Removes the option
	 * @param   Option|string $option
	 * @return  $this
	 */
	public function removeOption($option) {

		if ($option instanceof Option) {
			foreach ($this->options as $i => $o) {
				if ($o == $option) {
					unset($this->options[$i]);
				}
			}
		} else {
			foreach ($this->options as $i => $o) {
				if ($option == $o->getShortName() || $option == $o->getLongName()) {
					unset($this->options[$i]);
				}
			}
		}

		return $this;
	}

	/**
	 * Validates that the console options/arguments meets the definition
	 * @param   Event   $event
	 * @return  $this
	 * @throws
	 */
	public function validate(Event $event) {

		$event_options = $event->getOptions();

		foreach ($this->getOptions() as $option) {
			if ($event->hasOption($option->getNames())) {
				if (array_key_exists($option->getShortName(), $event_options)) {
					unset($event_options[$option->getShortName()]);
				}
				if (array_key_exists($option->getLongName(), $event_options)) {
					unset($event_options[$option->getLongName()]);
				}
				//get the value
				$value = $event->getOption($option->getNames(), $option->getDefault());

				//check whether a value is allowed
				if (!$option->isValueAllowed() && !is_null($value)) {
					throw new \InvalidArgumentException("Option \"{$option->getName()}\" cannot have a value.");
				}

				//check whether a value is required
				if ($option->isValueRequired() && is_null($value)) {
					throw new \InvalidArgumentException("Option \"{$option->getName()}\" requires a value.");
				}

				//filter the value
				$filter = $option->getFilter();
				if ($filter) {
					$value = call_user_func($filter, $value);
				}

				//update the value
				if ($event->hasOption($option->getShortName())) {
					$event->setOption($option->getShortName(), $value);
				} else {
					$event->setOption($option->getLongName(), $value);
				}

				//validate the value
				$validator = $option->getValidator();
				if ($validator) {
					if (call_user_func($validator, $value) == false) {
						throw new \InvalidArgumentException("Value \"{$value}\" for option \"{$option->getName()}\" is invalid.");
					}
				}

			} else {
				//check whether the option is required
				if ($option->isRequired()) {
					throw new \InvalidArgumentException("Option \"{$option->getName()}\" is required.");
				}

				//get the default value
				$default = $option->getDefault();

				//set the default value
				if ($option->isValueAllowed() && !is_null($default)) {
					$event->setOption($option->getLongName(), $default);
				}

			}

		}
		// See if there was an unexpected argument
		if (count($event_options) > 0) {
			$invalid_options = join(" ", array_keys($event_options));
			throw new \InvalidArgumentException("Option(s) {$invalid_options} unknown.");
		}

	}
}
