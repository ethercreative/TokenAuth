<?php

namespace Craft;


class Login_Node extends \Twig_Node
{
	public function compile(\Twig_Compiler $compiler)
	{
		$compiler
			->addDebugInfo($this)
			->write("\Craft\craft()->tokenAuth_auth->loginUser();\n");
	}
}