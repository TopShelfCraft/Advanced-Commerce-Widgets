<?php
namespace topshelfcraft\commercewidgets\stats;

use yii\db\Expression;

class AverageOrderTotal extends BaseStat
{

	/**
	 * @inheritdoc
	 */
	protected $_handle = 'advancedCommerceWidgets_averageOrderTotal';

	/**
	 * @inheritDoc
	 */
	public function getData()
	{
		$query = $this->_createStatQuery();
		$query->select([new Expression('ROUND(SUM([[total]]) / COUNT([[orders.id]]), 4) as averageOrderTotal')]);

		return $query->scalar();
	}

}