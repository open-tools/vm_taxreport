<?php
if (!defined ('_JEXEC')) die('Direct Access to ' . basename (__FILE__) . ' is not allowed.');

if (!class_exists ('VmModel')) require(VMPATH_ADMIN . DS . 'helpers' . DS . 'vmmodel.php');

class VirtuemartModelTaxReport extends VmModel {
	public $from_period  = '';
	public $until_period = '';

	function __construct () {
		parent::__construct ();
		$this->setMainTable ('orders');

		$this->removevalidOrderingFieldName ('virtuemart_order_id');
		$this->addvalidOrderingFieldName (array('`country`', '`taxrule`', '`taxrate`', '`ordercount`', '`sum_revenue_net`', '`sum_order_tax`'));
		$this->_selectedOrdering = '`country`';
	}


	function correctTimeOffset(&$inputDate) {
// 		$config = JFactory::getConfig();
// 		$this->siteOffset = $config->get('offset');
		$date = new JDate($inputDate);
		$date->setTimezone($this->siteTimezone);
// 		$inputDate = $date->format('Y-m-d H:i:s',true);
		$inputDate = $date->toSql(true);
	}

	function  setPeriod () {
		$lastmonth = mktime(0, 0, 0, date("m")-1, date("d")+1,   date("Y"));
		$this->from_period = vRequest::getVar ('from_period', date("Y-m-d", $lastmonth));
		$this->until_period = new JDate(vRequest::getVar ('until_period', date("Y-m-d")));
vmDebug("Until_period: ".$this->until_period);
// 		$this->until_period = strtotime('+1 days -1 second', $this->until_period);

		$config = JFactory::getConfig();
		$siteOffset = $config->get('offset');
		$this->siteTimezone = new DateTimeZone($siteOffset);

		$this->correctTimeOffset($this->from_period);
		$this->correctTimeOffset($this->until_period);
	}

	function getTaxes() {
		$user = JFactory::getUser();
		if($user->authorise('core.admin', 'com_virtuemart') or $user->authorise('core.manager', 'com_virtuemart')){
			$vendorId = vRequest::getInt('virtuemart_vendor_id');
		} else {
			$vendorId = VmConfig::isSuperVendor();
		}
		$this->setPeriod();

		$orderstates = vRequest::getVar ('order_status_code', array('C','S'));

		$mainTable = "`#__virtuemart_orders` AS `o`";
		$joins = array();
		$joins[] = "LEFT  JOIN #__virtuemart_order_userinfos  AS `ui` ON  `o`.`virtuemart_order_id`      = `ui`.`virtuemart_order_id` ";
		$joins[] = "LEFT  JOIN #__virtuemart_countries        AS `c`  ON `ui`.`virtuemart_country_id`    =  `c`.`virtuemart_country_id` ";
		$joins[] = "INNER JOIN #__virtuemart_order_items      AS `oi` ON  `o`.`virtuemart_order_id`      = `oi`.`virtuemart_order_id` ";
		$joins[] = "INNER JOIN #__virtuemart_order_calc_rules AS `cr` ON `oi`.`virtuemart_order_item_id` = `cr`.`virtuemart_order_item_id` ";
		
		$select = array();
		$select[] = "`c`.`country_name` AS `country`";
		$select[] = "`cr`.`calc_rule_name` AS `taxrule`";
		$select[] = "`cr`.`calc_value` AS `taxrate`";
		$select[] = "COUNT(DISTINCT `o`.`virtuemart_order_id`) as `ordercount`";
		$select[] = "SUM(`oi`.`product_quantity` * `oi`.`product_priceWithoutTax`) AS `sum_revenue_net`";
		$select[] = "SUM(`cr`.`calc_result`) AS `sum_order_tax`";

		$where = array();
		$where[] = '`ui`.`address_type` = "BT"'; // Otherwise, amounts will be double due to summation!
		// Order status:
		$ostatus = array();
		foreach ($orderstates as $s) {
			$ostatus[] = '`o`.`order_status` = "' . $s . '"';
		}
		if ($ostatus) {
			$where[] = "(" . join(" OR ", $ostatus) . ")";
		}
		$where[] = ' DATE( o.created_on ) BETWEEN "' . $this->from_period . '" AND "' . $this->until_period . '" ';
		$where[] = '`cr`.`calc_result` > 0';
		
		$groupbys = array("`c`.`virtuemart_country_id`", "`cr`.`virtuemart_calc_id`", "`cr`.`calc_currency`");
		
		$selectString = join(', ', $select) . ' FROM ' . $mainTable;
		$joinedTables = join('', $joins);
		$whereString = 'WHERE ' . join(' AND ', $where);
		$groupBy = "GROUP BY ".join(', ', $groupbys);
		$orderBy = $this->_getOrdering ();
		
vmDebug("SQL: SELECT ".$selectString.$joinedTables.$whereString.$groupBy.$orderBy);
		return $this->exeSortSearchListQuery (1, $selectString, $joinedTables, $whereString, $groupBy, $orderBy);
	}

}