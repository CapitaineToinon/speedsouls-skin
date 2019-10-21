<?php
/**
 * Skin file for skin SpeedSouls.
 *
 * @file
 * @ingroup Skins
 */

 /**
 * SkinTemplate class for SpeedSouls skin
 * @ingroup Skins
 */
class SkinSpeedSouls extends SkinTemplate {
	var $skinname = 'speedsouls', $stylename = 'SpeedSouls',
		$template = 'SpeedSoulsTemplate';

	/**
	 * This function adds JavaScript via ResourceLoader
	 *
	 * Use this function if your skin has a JS file(s).
	 * Otherwise you won't need this function and you can safely delete it.
	 *
	 * @param OutputPage $out
	 */
	public function initPage( OutputPage $out ) {
		parent::initPage( $out );
		$out->addModules( 'skins.speedsouls.js' );
		/* 'skins.foobar.js' is the name you used in your skin.json file */
	}

	/**
	 * Add CSS via ResourceLoader
	 *
	 * @param $out OutputPage
	 */
	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );
		$out->addModuleStyles( array(
			'mediawiki.skinning.interface', 'skins.speedsouls'
			/* 'skins.foobar' is the name you used in your skin.json file */
		) );
	}
}
