<?php
namespace Craft;

require_once('ReturnJson_Node.php');

class ReturnJson_Parser extends \Twig_TokenParser {

	public function parse(\Twig_Token $token)
	{
		$lineno = $token->getLine();
		$this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);

		return new ReturnJson_Node(array(), array(), $lineno, $this->getTag());
	}

	public function getTag()
	{
		return 'returnJson';
	}
}