<?php
namespace topshelfcraft\commercewidgets\stats;

class TotalOrders extends \craft\commerce\stats\TotalOrders
{

	use AdvancedStatTrait;

	/**
	 * @inheritdoc
	 */
	protected $_handle = 'advancedCommerceWidgets_totalOrders';

}
