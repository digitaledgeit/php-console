<?php

namespace deit\console\definition;
use deit\console\ConsoleInterface;

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
	 * Validates that the console options/arguments meets the definition
	 * @param   ConsoleInterface $console
	 * @return  $this
	 * @throws
	 */
	public function validate(ConsoleInterface $console) {

		foreach ($this->getOptions() as $option) {

			if ($console->hasOption($option->getNames())) {

				//get the value
				$value = $console->getOption($option->getNames(), $option->getDefault());

				//check whether a value is required
				if ($option->isValueRequired() && is_null($value)) {
					throw new \RuntimeException("Option \"{$option->getName()}\" requires a value.");
				}

				//filter the value
				$filter = $option->getFilter();
				if ($filter) {
					$value = call_user_func($filter, $value);
				}

				//update the value
				$console->setOption($option->getLongName(), $value); //TODO: what if they used short param

				//validate the value
				$validator = $option->getValidator();
				if ($validator) {
					if (call_user_func($validator, $value) == false) {
						throw new \RuntimeException("Option \"{$option->getName()}\" is invalid.");
					}
				}

			} else {

				//check whether the option is required
				if ($option->isRequired()) {
					throw new \RuntimeException("Option \"{$option->getName()}\" is required.");
				}

				//update the value
				$console->setOption($option->getLongName(), $option->getDefault());

			}

		}
		
	}

} 