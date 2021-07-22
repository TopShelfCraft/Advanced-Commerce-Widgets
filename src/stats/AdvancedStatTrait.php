<?php
namespace topshelfcraft\commercewidgets\stats;

use craft\commerce\db\Table;
use craft\db\Query;
use craft\helpers\DateTimeHelper;
use craft\helpers\Db;
use yii\db\Expression;

trait AdvancedStatTrait
{

	/**
	 * @var callable
	 */
	protected $_cacheNamespace;

	/**
	 * @var callable
	 */
	protected $_queryModifier;

	public function getHandle(): string
	{
		return parent::getHandle() . $this->_cacheNamespace;
	}

	public function setCacheNamespace(string $key)
	{
		$this->_cacheNamespace = $key;
	}

	public function setQueryModifier(callable $modifier)
	{
		$this->_queryModifier = $modifier;
	}

	protected function _createStatQuery(): Query
	{

		$query = (new Query)
			->from(Table::ORDERS . ' orders')
			->innerJoin('{{%elements}} elements', '[[elements.id]] = [[orders.id]]')
			->where(['>=', 'dateOrdered', Db::prepareDateForDb($this->getStartDate())])
			->andWhere(['<=', 'dateOrdered', Db::prepareDateForDb($this->getEndDate())])
			->andWhere(['isCompleted' => 1])
			->andWhere(['elements.dateDeleted' => null]);

		if ($this->_queryModifier)
		{
			($this->_queryModifier)($query);
		}

		return $query;

	}

}
