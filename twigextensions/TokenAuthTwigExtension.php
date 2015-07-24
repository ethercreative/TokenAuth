<?php
namespace Craft;

require_once('RequireJwt_Parser.php');
require_once('Login_Parser.php');
require_once('Post_Parser.php');
require_once('ReturnJson_Parser.php');

class TokenAuthTwigExtension extends \Twig_Extension {

	public function getName()
	{
		return 'tokenAuth';
	}

	public function getTokenParsers()
	{
		return array(
			new RequireJwt_Parser(),
			new Login_Parser(),
			new Post_Parser(),
			new ReturnJson_Parser()
		);
	}

}