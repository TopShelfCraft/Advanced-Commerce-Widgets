<?php
namespace topshelfcraft\commercewidgets\stats;

class TotalRevenue extends \craft\commerce\stats\TotalRevenue
{

	use AdvancedStatTrait;

	/**
	 * @inheritdoc
	 */
	protected $_handle = 'advancedCommerceWidgets_totalRevenue';

}
