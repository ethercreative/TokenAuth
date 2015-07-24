<?php
namespace Craft;

require_once('Post_Node.php');

class Post_Parser extends \Twig_TokenParser {

	public function parse(\Twig_Token $token)
	{
		$lineno = $token->getLine();
		$this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);

		return new Post_Node(array(), array(), $lineno, $this->getTag());
	}

	public function getTag()
	{
		return 'tokenAuthPost';
	}
}