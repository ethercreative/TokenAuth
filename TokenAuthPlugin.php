<?php
namespace Craft;

class TokenAuthPlugin extends BasePlugin {

	public function getName()
	{
		return Craft::t('Token Auth');
	}

	public function getVersion()
	{
		return '0.1.1';
	}

	public function getDeveloper()
	{
		return 'Ether Creative';
	}

	public function getDeveloperUrl()
	{
		return 'http://ethercreative.co.uk/';
	}

	protected function defineSettings()
	{
		return array(
			'secret' => array(AttributeType::String, 'default' => '53cr37'),
			'expirationTime' => array(AttributeType::Number, 'default' => -1),
		);
	}

	public function getSettingsHtml()
	{
		return craft()->templates->render('TokenAuth/settings', array(
			'settings' => $this->getSettings()
		));
	}

	public function init()
	{
		parent::init();

		require CRAFT_PLUGINS_PATH.'/TokenAuth/vendor/autoload.php';
	}

	public function addTwigExtension()
	{
		Craft::import('plugins.TokenAuth.twigextensions.TokenAuthTwigExtension');
		return new TokenAuthTwigExtension();
	}
}