<?php
namespace topshelfcraft\commercewidgets\stats;

class AverageOrderTotal extends \craft\commerce\stats\AverageOrderTotal
{

	use AdvancedStatTrait;

	/**
	 * @inheritdoc
	 */
	protected $_handle = 'advancedCommerceWidgets_averageOrderTotal';

}
