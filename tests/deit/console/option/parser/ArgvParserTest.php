<?php

namespace deit\console\parser;
use \deit\console\Event;

/**
 * Parser test
 * @author James Newell <james@digitaledgeit.com.au>
 */
class ArgvParserTest extends \PHPUnit_Framework_TestCase {

	public function test() {

		$console    = new Event();
		$argv       = array(
			'my-cmd.php',
			'-abc',
			'-d=v',
			'-test',
			'--opt=test',
		);

		$parser     = new ArgvParser($argv);
		$parser->parse($console);

		$this->assertEquals(null, $console->getOption('a'));

	}

}
 
