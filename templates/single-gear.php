<?php
/**
 * Plugin Name: Scoutbook  Gear
 * Plugin URI: http://troop351.org/plugins/scoutbook-gear
 * Description:
 * Version: 0.1
 * Author: Phil Newman
 * Author URI: http://getyourphil.net
 * License: GPL3$classes[] = $slug;
 * License URI: http://www.gnu.org/licenses/gpl-3.0.en.html
 **/

global $post;
$gear = get_post();
$gearName = get_post_meta($gear->ID, 'gearName', true);

if (wp_is_mobile()){
  echo '<head>';
  wp_head();
  echo '<meta name="viewport" content="width=device-width, initial-scale=1.0"/></head>';
      
  $min_date = date("Y-m-d");
  $max_date =  date('Y-m-d',strtotime("+3 months"));

  $checkoutable = isGearCheckedOut($gear->ID);
  if ($checkoutable){
    $action = 'checkin';
  } else { 
    $action = 'checkout';
  }
    
  ?>
  <body <?php body_class('checkinout'); ?>>
  <div id="ptn_scoutbook_checkinout">
    <form action="http://dev.troop351.org/wp-admin/admin-post.php" method="post">
      <?php if (!$checkoutable){ ?>
        <label>User:</label> <input type="text" name="user" value="" />
        <label>Date:</label> <input type="date" name="checkOut" value="" min="<?php echo $min_date; ?>" max="<?php echo $max_date; ?>"/>
        <label>Notes:</label><textarea name="notes" ></textarea>
      <?php  }else{ ?>
        <label>Date:</label> <input type="date" name="checkIn" value="<?php echo date("Y-m-d"); ?>" min="<?php echo date("Y-m-d"); ?>" max="<?php echo date("Y-m-d"); ?>" />
        <label>Notes:</label> <textarea name="notes"  autofocus/></textarea>
      <?php  } ?>
      <input type="hidden" name="gearID" value="<?php echo $gear->ID; ?>" />
      <input type="hidden" name="action" value="<?php echo $action;?>" /> 
      <button type="submit" name="submit" value="submit">Check <?php echo substr($action, 5); ?> Gear</button>  
    </form>
  </div><!--ptn_scoutbook_checkinout-->
</body>
 <?php  
}else{
  wp_head();
  $gearStorage = get_post_meta($gear->ID, 'gearStorage', true);
  $gearHistory = get_post_meta($gear->ID, 'gearDetails', true);
?>
 <body <?php body_class(); ?>>
   <?php get_header(); ?>
    <div class="post-inner-content post-type-gear">
      <h1 class="entry-title"><?php echo $gearName;?></h1>
      <label>Barcode ID: </label><?php echo ' '.$gear->ID; ?></p>
        <label>Storage Location: </label><?php echo ' '.$gearStorage;?>
      <?php $content = apply_filters('the_content', $gear->post_content); ?>
      <div class="entry-content"><?php echo $content;?></div>
      <div id="gear_history">
        <?php if (empty($gearHistory)){ ?>
        <h3>This gear has no checkout history.</h3>
        <?php }else{ ?>
        <table style="padding:15px;">
          <tr><th>User</th><th>CheckOut Date</th><th>CheckIn Date</th><th>Notes</th></tr>
          <?php  foreach($gearHistory as $checkOutRecord){ ?>
            <tr>
              <td><?php echo $checkOutRecord['user'];?></td>
              <td><?php echo $checkOutRecord['checkout'];?></td>
              <td><?php echo $checkOutRecord['checkin'];?></td>
              <td><?php echo $checkOutRecord['notes'];?></td>
            </tr>
            <?php }
            }?>
         </table>
     </div> <!--gear_history-->
    </div>
   <?php get_sidebar(); ?>
  <?php  get_footer();
  ?></body><?php
}  
?>

