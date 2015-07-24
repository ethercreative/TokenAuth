<?php

namespace Craft;


class Post_Node extends \Twig_Node
{
	public function compile(\Twig_Compiler $compiler)
	{
		$compiler
			->addDebugInfo($this)
			->write("\Craft\craft()->tokenAuth_auth->post();\n");
	}
}