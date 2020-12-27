<?php
namespace topshelfcraft\commercewidgets\web\widgets;

use craft\i18n\Locale;

trait ImprovedWidgetTrait
{

	public $dateRange;

	public $customStartDate;

	public $customEndDate;

	/**
	 * @inheritDoc
	 */
	public function getDateRangeWording(): string
	{

		switch ($this->dateRange) {
			case self::DATE_RANGE_ALL:
			{
				return Craft::t('commerce', 'All');
				break;
			}
			case self::DATE_RANGE_TODAY:
			{
				return Craft::t('commerce', 'Today');
				break;
			}
			case self::DATE_RANGE_THISWEEK:
			{
				return Craft::t('commerce', 'This week');
				break;
			}
			case self::DATE_RANGE_THISMONTH:
			{
				return Craft::t('commerce', 'This month');
				break;
			}
			case self::DATE_RANGE_THISYEAR:
			{
				return Craft::t('commerce', 'This year');
				break;
			}
			case self::DATE_RANGE_PAST7DAYS:
			{
				return Craft::t('commerce', 'Past {num} days', ['num' => 7]);
				break;
			}
			case self::DATE_RANGE_PAST30DAYS:
			{
				return Craft::t('commerce', 'Past {num} days', ['num' => 30]);
				break;
			}
			case self::DATE_RANGE_PAST90DAYS:
			{
				return Craft::t('commerce', 'Past {num} days', ['num' => 90]);
				break;
			}
			case self::DATE_RANGE_PASTYEAR:
			{
				return Craft::t('commerce', 'Past year');
				break;
			}
			case self::DATE_RANGE_CUSTOM:
			{
				if (!$this->_startDate || !$this->_endDate) {
					return '';
				}

				$startDate = Craft::$app->getFormatter()->asDate($this->_startDate, Locale::LENGTH_SHORT);
				$endDate = Craft::$app->getFormatter()->asDate($this->_endDate, Locale::LENGTH_SHORT);

				if (Craft::$app->getLocale()->getOrientation() == 'rtl') {
					return $endDate . ' - ' . $startDate;
				}

				return $startDate . ' - ' . $endDate;
				break;
			}
			default:
			{
				return '';
				break;
			}
		}

	}

}
