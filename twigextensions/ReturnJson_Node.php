<?php

namespace Craft;


class ReturnJson_Node extends \Twig_Node
{
	public function compile(\Twig_Compiler $compiler)
	{
		$compiler
			->addDebugInfo($this)
			->write("\Craft\JsonHelper::sendJsonHeaders();\n");
	}
}