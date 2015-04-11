<?php
if( !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

AdminUIHelper::startAdminArea($this);

JHtml::_('behavior.framework', true);
if (!class_exists('CurrencyDisplay'))
    require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');
$myCurrencyDisplay = CurrencyDisplay::getInstance();
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

    <div id="header">
        <h2><?php echo vmText::sprintf('VMEXT_TAXREPORT_VIEW_TITLE_DATE', vmJsApi::date( $this->from_period, 'LC',true) , vmJsApi::date( $this->until_period, 'LC',true) ); ?></h2>
        
        <div id="filterbox">
            <table>
                <tr>
                    <td align="left" width="100%">
						<?php 
						echo vmText::_('COM_VIRTUEMART_ORDERSTATUS') . $this->lists['state_list']; 
						echo vmText::_('COM_VIRTUEMART_REPORT_FROM_PERIOD') .  vmJsApi::jDate($this->from_period, 'from_period');
                   echo vmText::_('COM_VIRTUEMART_REPORT_UNTIL_PERIOD') . vmJsApi::jDate($this->until_period, 'until_period'); ?>
                        <button class="btn btn-small" onclick="this.form.submit();"><?php echo vmText::_('COM_VIRTUEMART_GO'); ?>
                        </button>
                    </td>
                </tr>
            </table>
        </div>
        <div id="resultscounter">
            <?php if ($this->pagination) echo $this->pagination->getResultsCounter();?>
        </div>
    </div>

    <div id="editcell">
	    <table class="adminlist table table-striped" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th><?php echo $this->sort('`country`', 'VMEXT_TAXREPORT_COUNTRY') ; ?></th>
                    <th><?php echo $this->sort('`taxrule`', 'VMEXT_TAXREPORT_TAXNAME') ; ?></th>
                    <th><?php echo $this->sort('`taxrate`', 'VMEXT_TAXREPORT_TAXRATE') ; ?></th>
                    <th><?php echo $this->sort('`ordercount`', 'VMEXT_TAXREPORT_ORDERS') ; ?></th>
                    <th><?php echo $this->sort('`sum_revenue_net`', 'VMEXT_TAXREPORT_ORDERREVENUENET') ; ?></th>
                    <th><?php echo $this->sort('`sum_order_tax`', 'VMEXT_TAXREPORT_ORDERTAXES') ; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
        $i = 0;
        foreach ($this->report as $r) { ?>
                <tr class="row<?php echo $i;?>">
                    <td align="center"><?php echo $r['country']; ?></td>
                    <td align="left"><?php echo $r['taxrule']; ?></td>
                    <td align="center"><?php echo round($r['taxrate'], 4) . " %"; ?></td>
                    <td align="center"><?php echo $r['ordercount']; ?></td>
                    <td align="right"><?php echo $myCurrencyDisplay->priceDisplay($r['sum_revenue_net']); ?></td>
                    <td align="right"><?php echo $myCurrencyDisplay->priceDisplay($r['sum_order_tax']); ?></td>
                </tr>
                <?php 
                $i = 1-$i; 
	    } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10">
                        <?php if ($this->pagination) echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

	<?php echo $this->addStandardHiddenToForm(); ?>
</form>

<?php AdminUIHelper::endAdminArea(); ?>