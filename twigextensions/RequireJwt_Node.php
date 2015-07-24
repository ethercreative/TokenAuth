<?php
namespace Craft;

class RequireJwt_Node extends \Twig_Node
{
	public function compile(\Twig_Compiler $compiler)
	{
		$compiler
			->addDebugInfo($this)
			->write("\Craft\craft()->tokenAuth_auth->checkAuth();\n");
	}
}
