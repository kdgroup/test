<?php
/* ------------------------------------------------------------------------
  # En Masse - Social Buying Extension 2010
  # ------------------------------------------------------------------------
  # By Matamko.com
  # Copyright (C) 2010 Matamko.com. All Rights Reserved.
  # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.matamko.com
  # Technical Support:  Visit our forum at www.matamko.com
  ------------------------------------------------------------------------- */
// No direct access 
defined( '_JEXEC' ) or die( 'Restricted access' ); 

?>

<body>
<form action='<?php echo "http://".$_SERVER['HTTP_HOST'];?>/~traderpo/new/discounts/index.php?option=com_enmasse&controller=payment&task=notifyUrl&payClass=realex' method='post' name='returnURLForm'>
<?php
//<form action='index.php?option=com_enmasse&controller=payment&task=notifyUrl&payClass=realex' method='post' name='returnURLForm'>
foreach ($_POST as $a => $b) {
	print("<input type='hidden' name='".$a."' value='".$b."'/>");
}
foreach ($_GET as $a => $b) {
	print("<input type='hidden' name='".$a."' value='".$b."'/>");
} 
?>
</form>
<script language="JavaScript">
document.returnURLForm.submit();
</script>
</body>
