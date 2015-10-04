<?php

namespace deit\console\parser;
use \deit\console\Event;
use \deit\console\definition\Definition;
use \deit\console\definition\Option;
//use \deit\console\command\Command;

/**
 * Parser test
 * @author James Newell <james@digitaledgeit.com.au>
 */
class DefinitionValidateRequiredOptionTest extends \PHPUnit_Framework_TestCase {

	const OPTION_UPLOAD  = 'upload';
	const OPTION_OUTPUT  = 'o|output';
	const EVENT_DISPATCH = 'dispatch';

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function test_validator_catches_missing_required_argument() {

		//create the event
		$event = new Event();
		$event
			->setOptions(array())
			->setArguments(array())
		;

		//create the command's definition
		$definition = new Definition();
		$definition
			->addOption(new Option(self::OPTION_OUTPUT, Option::OPTION_REQUIRED));
		;

		//setup the command-line arguments for the event
		$argv = array(
		        'my-cmd.php',
		        );

		//parse the command-line arguments
		$parser = new ArgvParser($argv);
		$parser->parse($event);

		//make sure the defintion validator is unhappy
		$definition->validate($event);

	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function test_validator_catches_extra_argument() {

		//create the event
		$event = new Event();
		$event
			->setOptions(array())
			->setArguments(array())
		;

		//create the command's definition
		$definition = new Definition();
		$definition
			->addOption(new Option(self::OPTION_OUTPUT))
		;

		//setup the command-line arguments for the event
		$argv = array(
		              'my-cmd.php',
		              '--unexpected_arg',
		             );

		//parse the command-line arguments
		$parser = new ArgvParser($argv);
		$parser->parse($event);

		//make sure the defintion validator is unhappy
		$definition->validate($event);

	}

}

