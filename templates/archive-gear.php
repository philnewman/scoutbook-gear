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
?>
<?php wp_head(); ?>
<?php get_header(); ?>
<?php
$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
$args = array(
    'post_type' => 'gear',
     'paged' => $paged,
);
$my_query = new WP_Query( $args );
?>
<div class="wrap">
<div id="primary" class="content-area">
<main id="main" class="site-main" role="main">
    <div class="post-inner-content">    
      <h1 class="entry-title">Troop 351: Gear Inventory</h1>
<?php if ( $my_query->have_posts() ) { ?>
    <table style="padding:15px;">
      <tr>
        <th>BARCODE ID</th>
        <th>GEAR</th>
        <th>STORAGE</th>
        <th>CHECK IN/OUT</th>
      </tr> <!--gear_header-->
  <?php while($my_query->have_posts()) :
       $my_query->the_post();
        // Post data goes here.
        $id = get_the_ID();
        $name = get_post_meta($id, 'gearName', true);
        $location = get_post_meta($id, 'gearStorage', true);
        $title = get_the_title(); 
        $inOut = isGearCheckedOut($id);
        if ($inOut){
          $lastUsed = lastUser($id);
          $checkoutstatus = 'Checked out by: '.$lastUsed['name'].' on '.$lastUsed['checkout'].'.';
        }  else {
          $checkoutstatus = 'Checked in.';
        } 
  ?>
    
      <tr>
        <td><a href="/index.php/gear/<?php echo $title; ?>"><?php echo $title; ?></a></td>
        <td><?php echo ' '.$name; ?></td>
        <td><?php echo ' '.$location; ?></td>
        <td><?php echo ' '.$checkoutstatus; ?></td>
      </tr><!--row-->

    <?php endwhile; ?>
    </table> <!--table-->

      <div class="pagination">
        <?php
        echo paginate_links( array(
            'format'  => 'page/%#%',
            'current' => $paged,
            'total'   => $my_query->max_num_pages,
            'mid_size'        => 2,
            'prev_text'       => __('&laquo; Prev Page'),
            'next_text'       => __('Next Page &raquo;')
        ) );
        ?>
    </div>
  </div>
<?php }else{ ?>
  <div>'No posts found';</div>
<?php } 
?>
</main>
</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
