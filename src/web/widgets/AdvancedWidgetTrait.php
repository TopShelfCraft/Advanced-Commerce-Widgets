<?php
namespace topshelfcraft\commercewidgets\web\widgets;

trait AdvancedWidgetTrait
{

	/**
	 * @var string
	 */
	public $customTitle = "{title}";

	/**
	 * @var string
	 */
	public $customSubtitle = "{subtitle}";

	/**
	 * @var string
	 */
	public $queryModifierKey;

	/**
	 * @inheritDoc
	 */
	public static function maxColspan()
	{
		return null;
	}

}
