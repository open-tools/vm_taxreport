<?php
defined('_JEXEC') or die('Restricted access');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!class_exists( 'VmConfig' )) 
    require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');

class plgVmExtendedTaxReportInstallerScript
{
	protected $_type = '';
	protected $_name = '';

	public function __construct () {
		$this->_type = 'vmextended';
		$this->_name = 'taxreport';
	}

	public function loadLanguage($extension = '', $basePath = JPATH_ADMINISTRATOR)
	{
		if (empty($extension))
		{
			$extension = 'plg_' . $this->_type . '_' . $this->_name;
		}

		$lang = JFactory::getLanguage();

		return $lang->load(strtolower($extension), $basePath, null, false, true)
			|| $lang->load(strtolower($extension), JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name, null, false, true);
	}
	
			
    public function postflight ($type, $parent = null) {
        $this->loadLanguage();
        $db = JFactory::getDBO();
        $db->setQuery("SELECT `id` FROM `#__virtuemart_adminmenuentries` WHERE `view` = '".$this->_name."'");
        $exists = $db->loadResult();
        if (!$exists) {
            $q = "INSERT INTO `#__virtuemart_adminmenuentries` (`module_id`, `name`, `link`, `depends`, `icon_class`, `ordering`, `published`, `tooltip`, `view`, `task`) VALUES
(2, '" . vmText::_('COM_VIRTUEMART_TAXREPORT') . "', '', '', 'vmicon vmicon-16-report', 25, 1, '', '" . $this->_name .  "', '')";
            $db->setQuery($q);
            $db->query();
        }
    }
 
    public function install(JAdapterInstance $adapter)
    {
        $db = JFactory::getDBO();
        $db->setQuery('update #__extensions set enabled = 1 where type = "plugin" and element = "' . $this->_name . '" and folder = "' .  $this->_type . '"');
        $db->query();
        return True;
    }
 
    public function uninstall(JAdapterInstance $adapter)
    {
        $db = JFactory::getDBO();
        $q = "DELETE FROM `#__virtuemart_adminmenuentries` WHERE `view` = '" . $this->name . "' AND `task` = '' AND `module_id` = 2";
        $db->setQuery($q);
        $db->query();
    }
}
