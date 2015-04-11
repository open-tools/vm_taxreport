<?php
if( !defined( '_JEXEC' ) ) die('Restricted access');

if(!defined('VM_VERSION') or VM_VERSION < 3){
	// VM2 has class VmView instead of VmViewAdmin:
	if(!class_exists('VmView'))      require(VMPATH_ADMIN.DS.'helpers'.DS.'vmview.php');
	class VmViewAdmin extends VmView {}
	defined ('VMPATH_PLUGINLIBS') or define ('VMPATH_PLUGINLIBS', JPATH_VM_PLUGINS);
} else {
	if(!class_exists('VmViewAdmin')) require(VMPATH_ADMIN.DS.'helpers'.DS.'vmviewadmin.php');
}

class VirtuemartViewTaxReport extends VmViewAdmin {
	function __construct(){
		parent::__construct();
		$this->_addPath('template', JPATH_PLUGINS.DS . 'vmextended' . DS . 'taxreport' . DS . 'views' . DS . $this->getName() . DS  . 'tmpl');
	}

	function display($tpl = null){

		if (!class_exists('VmHTML')) require(VMPATH_ADMIN . DS . 'helpers' . DS . 'html.php');
		if (!class_exists('CurrencyDisplay')) require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');

		vRequest::setvar('task','');
		$this->SetViewTitle('TAXREPORT');

		$model		= VmModel::getModel();
		$this->addStandardDefaultViewLists($model);
		$myCurrencyDisplay = CurrencyDisplay::getInstance();
		
		$taxData = $model->getTaxes();
		$this->assignRef('report', $taxData);
		
		$orderstatusM =VmModel::getModel('orderstatus');
		$orderstates = vRequest::getVar ('order_status_code', array('C','S'));
		$this->lists['state_list'] = $orderstatusM->renderOSList($orderstates,'order_status_code',TRUE);
		
		$this->assignRef('from_period', $model->from_period);
		$this->assignRef('until_period', $model->until_period);
		
		$this->pagination = $model->getPagination();

		parent::display($tpl);
	}
	
}