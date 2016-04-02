<?php
if( !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

defined ('VMPATH_PLUGINLIBS') or define ('VMPATH_PLUGINLIBS', JPATH_VM_PLUGINS);
if (!class_exists('vmExtendedPlugin')) require(VMPATH_PLUGINLIBS . DS . 'vmextendedplugin.php');

class plgVmExtendedTaxReport extends vmExtendedPlugin {

	public function __construct (&$subject, $config=array()) {
		parent::__construct($subject, $config);
		$this->_path = JPATH_PLUGINS.DS.'vmextended'.DS.$this->getName();
		$this->loadLanguage('plg_vmextended_'.$this->getName());
	}

	public function onVmAdminController ($controller) {
		if ($controller == 'taxreport'/*$this->getName()*/) {
			VmModel::addIncludePath($this->_path . DS . 'models', 'VirtueMartModel');
			require_once($this->_path.DS.'controllers'.DS.'taxreport.php');
			
			// In later VM versions, we can/should execute the controller here rather than letting virtuemart.php do the job:
			$_class = 'VirtueMartController'.ucfirst($controller);
			if(!class_exists($_class)){
				vmError('Serious Error could not find controller '.$_class,'Serious error, unable to find class');
				$app = vFactory::getApplication();
				$app->redirect('index.php?option=com_virtuemart');
			}
			$controller = new $_class();
			$controller->execute(vRequest::getCmd('task', $controller));
			$controller->redirect();
			return true;
		}
	}

	public function onInstallCheckAdminMenuEntries() {
		$db = JFactory::getDBO();
		$db->setQuery("SELECT `id` FROM `#__virtuemart_adminmenuentries` WHERE `view` = 'taxreport'");
		$exists = $db->loadResult();
		if (!$exists) {
			$q = "INSERT INTO `#__virtuemart_adminmenuentries` (`module_id`, `name`, `link`, `depends`, `icon_class`, `ordering`, `published`, `tooltip`, `view`, `task`) VALUES
(2, '" . vmText::_('COM_VIRTUEMART_TAXREPORT') . "', '', '', 'vmicon vmicon-16-report', 25, 1, '', 'taxreport', '')";
			$db->setQuery($q);
			$db->query();
		}
	}

}