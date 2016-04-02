<?php
if( !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

defined ('VMPATH_ADMIN') or define ('VMPATH_ADMIN', JPATH_VM_ADMINISTRATOR);
if(!class_exists('VmController')) require(VMPATH_ADMIN.DS.'helpers'.DS.'vmcontroller.php');

class VirtuemartControllerTaxReport extends VmController {

	function __construct(){
		parent::__construct();
		// Add the proper view pathes...
		$this->addViewPath(JPATH_PLUGINS.DS . 'vmextended' . DS . 'taxreport' . DS . 'views' . DS . 'taxreport');
	}

}
