<?php
namespace topshelfcraft\commercewidgets\stats;

class TopProducts extends \craft\commerce\stats\TopProducts
{

	use AdvancedStatTrait;

	/**
	 * @inheritdoc
	 */
	protected $_handle = 'advancedCommerceWidgets_topProducts';

}
