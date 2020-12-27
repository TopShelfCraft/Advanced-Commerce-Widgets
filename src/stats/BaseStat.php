<?php
namespace topshelfcraft\commercewidgets\stats;

use craft\commerce\base\Stat;
use craft\commerce\db\Table;
use craft\db\Query;
use craft\helpers\DateTimeHelper;
use craft\helpers\Db;
use yii\db\Expression;

abstract class BaseStat extends Stat
{

	/**
	 * @var callable
	 */
	public $_queryModifier;

	/**
	 * @param $modifier
	 */
	public function setQueryModifier(callable $modifier)
	{
		$this->_queryModifier = $modifier;
	}

	/**
	 * @return \yii\db\Query
	 */
	protected function _createStatQuery()
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

	/**
	 * @param array $select
	 * @param array $resultsDefaults
	 * @param null|Query $query
	 *
	 * @return array|null
	 *
	 * @throws \Exception
	 */
	protected function _createChartQuery(array $select = [], array $resultsDefaults = [], $query = null): ?array
	{

		// Allow the passing in of a custom query in case we need to add extra logic
		$query = $query ?: $this->_createStatQuery();

		$defaults = [];
		$dateRangeInterval = $this->getDateRangeInterval();
		$options = $this->getChartQueryOptionsByInterval($dateRangeInterval);

		if (!$options) {
			return null;
		}

		$dateKeyDate = DateTimeHelper::toDateTime($this->getStartDate()->format('U'));
		$endDate = $this->getEndDate();
		while ($dateKeyDate <= $endDate) {
			$key = $dateKeyDate->format($options['dateKeyFormat']);

			// Setup default results values
			$tmp = $resultsDefaults;
			$tmp['datekey'] = $key;

			$defaults[$key] = $tmp;
			$dateKeyDate->add(new \DateInterval($options['interval']));
		}

		// Add defaults to select
		$select[] = new Expression($options['dateKey'] . ' as datekey');
		$results = $query
			->select($select)
			->groupBy(new Expression($options['groupBy']))
			->orderBy(new Expression($options['orderBy']))
			->indexBy('datekey')
			->all();

		$return = array_replace($defaults, $results);
		ksort($return, SORT_NATURAL);

		return $return;

	}

}
