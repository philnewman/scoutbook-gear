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
 function isGearCheckedOut($id){   
   $checkOutDetails = get_post_meta($id, 'gearDetails', true);
   if (is_array($checkOutDetails)){
    end($checkOutDetails );
    $last_id = key($checkOutDetails );  
   }   
     if (!empty($checkOutDetails [$last_id]['checkin']) || empty($checkOutDetails )) {
      $checkOut = FALSE; 
   }  else {
      $checkOut = TRUE;
   }
   return $checkOut; 
  }
  
 function lastUser($id){
   $lastUsed = get_post_meta($id, 'gearDetails', true);
   end($lastUsed);
   $last_id = key($lastUsed);
   
   $last_user = array(
    'name' => '',
    'checkout' => ''
   );
   $last_user['name'] = $lastUsed[$last_id]['user'];
   $last_user['checkout'] = $lastUsed[$last_id]['checkout'];
   return $last_user;
 }

?>