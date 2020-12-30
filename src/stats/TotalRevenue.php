<?php
namespace topshelfcraft\commercewidgets\stats;

use yii\db\Expression;

class TotalRevenue extends BaseStat
{

	/**
	 * @inheritdoc
	 */
	protected $_handle = 'advancedCommerceWidgets_totalRevenue';

	/**
	 * @inheritDoc
	 */
	public function getData()
	{

		return $this->_createChartQuery(
			[
				new Expression('SUM([[total]]) as revenue'),
				new Expression('COUNT([[orders.id]]) as count'),
			],
			[
				'revenue' => 0,
				'count' => 0,
			]
		);

	}

}
