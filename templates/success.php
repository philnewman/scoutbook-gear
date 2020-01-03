<?php
/**
 * Plugin Name: Scoutbook  Gear
 * Plugin URI: http://troop351.org/plugins/scoutbook-gear
 * Description:
 * Version: 0.1
 * Author: Phil Newman
 * Author URI: http://getyourphil.net
 * License: GPL3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.en.html
 **/

require_once("../../../../wp-load.php");

echo '<head>';
wp_head();
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/></head>
<?php
  $gearID = $_GET['id'];
  $gearInOut = $_GET['inout'];
  $gearName = get_post_meta($gearID, 'gearName', true);
  $gearBarCode =  get_the_title($gearID);
?>
 <div id="ptn_scoutbook_checkinout">
 <span class="result"><?php echo $gearName; ?> with barcode <?php echo $gearBarCode; ?> has been checked <?php echo $gearInOut; ?>. </span>
  <button onclick="window.location.href='https://dev.troop351.org/scan2/live_w_locator.html'">Check In/Out More Gear</button>
</div>
