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
class DefinitionValidateTest extends \PHPUnit_Framework_TestCase {

	const OPTION_UPLOAD = 'upload';
	const OPTION_OUTPUT         = 'o|output';
	const EVENT_DISPATCH = 'dispatch';

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function test_validator() {

		$argv       = array(
			'my-cmd.php',
			'-abc',
			'-d=v',
			'-test',
			'--opt=test',
		);

		//create the event
		$event = new Event();
		$event
			->setName(self::EVENT_DISPATCH)
			->setOptions(array())
			->setArguments(array())
		;


		//parse the command line arguments
		$parser = new ArgvParser();
		$parser->parse($event);

	   
	    $definition = new Definition(); 

		$outputDirectory = $_SERVER['HOME'].'/uf-dist';

		$definition
			->setName('dist:build-admin')
			->setDescription('Uploads the admin application')

			//upload option
			->addOption(new Option(self::OPTION_UPLOAD))
			//output directory option
			->addOption(new Option(self::OPTION_OUTPUT, Option::OPTION_REQUIRED, $outputDirectory, function($value) { return rtrim($value, '/\\'); }, function($value) {
				return is_dir($value);
			}))

		;

	    $definition->validate($event);

//		$this->assertEquals(null, $console->getOption('a'));

	}

}
 
