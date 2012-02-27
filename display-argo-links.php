<?php
/**
  * @package Argo_Links
  * @version 0.01
  */
/*
*Argo Links - Link Roundups Meta Box Code
*/
/** WordPress Administration Bootstrap */
require_once('../../../wp-admin/admin.php');

global $post;
// The Query
/*Build our query for what links to show!*/
$posts_per_page = (isset($_REQUEST['posts_per_page']) ? $_REQUEST['posts_per_page'] : 15);
$page = (isset($_REQUEST['argo_page']) ? $_REQUEST['argo_page']: 1);
$args =  array(
                  'post_type' => 'argolinks',
                  'orderby' => (isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'date'),
                  'order' => (isset($_REQUEST['order']) ? $_REQUEST['order'] : 'desc'),
                  'posts_per_page' => -1

                  );
$the_posts_count_query = new WP_Query($args);
$total_post_count = $the_posts_count_query->post_count;
$the_posts_count_query = '';
$the_query = new WP_Query(array_merge($args,array('posts_per_page' => $posts_per_page, 'paged' => $page)));

$from_result = 1;
$to_result = $posts_per_page;
if ($page != 1) {
  $from_result = $posts_per_page * ($page - 1);
  $to_result = $from_result + $posts_per_page - 1;
}
if ($to_result > $total_post_count) {
  $to_result = $total_post_count;
}

/*Build pagination links*/
$pagination_first = 1;
$pagination_previous = $page - 1;
$pagination_next = $page + 1;
$pagination_last = ceil($total_post_count / $posts_per_page);

$query_url = '';
$query_url .= (isset($_REQUEST['orderby']) ? '&orderby='.$_REQUEST['orderby']: '');
$query_url .= (isset($_REQUEST['order']) ? '&order='.$_REQUEST['order']: '');
?>
<div class='display-argo-links'>
  <div class='pagination' style='text-align:right'>
    Displaying <?php echo $from_result;?>-<?php echo $to_result;?> of <?php echo $total_post_count;?>
    <?php if(!($page <= 6)):?>
      <a href='argo_page=<?php echo $pagination_first;?><?php echo $query_url; ?>'><<</a>
    <?php endif; ?>
    <?php if(!($page == 1)):?>
      <a href=argo_page='<?php echo $pagination_previous;?><?php echo $query_url; ?>'><</a>
    <?php endif; ?>
    <?php
      $start = 1;
      $count = 0;
      if ($page == 1) {
        if ($pagination_last >= 11) {
          $count = $start + 11;
        } else {
          $count = $pagination_last;
        }
      } else if ($page == $pagination_last) {
        $start = $pagination_last - 11;
        $count = $start + 11;
      } else {
        if (($page + 5) > $pagination_last) {
          $start = $pagination_last - 10;
          $count = $start + 10;
        } else if (($page - 5) <= 0) {
          $start = 1;
          $count = 11;
        } else {
          $start = $page - 5;
          $count = $start + 10;
        }
      }
      while ($start <= $count) {
        echo "<a href='argo_page=$start$query_url' class='".($start == $page ? 'current' : '')."'>$start</a> &nbsp;";
        $start++;
      }
    ?>
    <?php if(!($page == $pagination_last)):?>
      <a href='argo_page=<?php echo $pagination_next;?><?php echo $query_url; ?>'>></a>
    <?php endif; ?>
    <?php if(!($page >= ($pagination_last - 5))):?>
      <a href='argo_page=<?php echo $pagination_last;?><?php echo $query_url; ?>'>>></a>
    <?php endif;?>
  </div>
  <table class="wp-list-table widefat fixed posts" cellspacing="0">
    <tr>
      <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" id='check-all-boxes'></th>
      <th scope="col" id="title" class="manage-column column-title <?php echo (isset($_REQUEST['orderby']) && $_REQUEST['orderby'] == 'title' ? 'sorted' : 'sortable');?> <?php echo (isset($_REQUEST['orderby']) ? ($_REQUEST['orderby'] == 'title' && $_REQUEST['order'] == 'desc' ? 'desc' : 'asc') : 'desc');?>" style=""><a href="post_type=argolinks&orderby=title&order=<?php echo (isset($_REQUEST['orderby']) ? ($_REQUEST['orderby'] == 'title' && $_REQUEST['order'] == 'desc' ? 'asc' : 'desc') : 'desc');?>"><span>Title</span><span class="sorting-indicator"></span></a></th>
      <th scope="col" id="author" class="manage-column column-author <?php echo (isset($_REQUEST['orderby']) && $_REQUEST['orderby'] == 'author' ? 'sorted' : 'sortable');?> <?php echo (isset($_REQUEST['orderby']) ? ($_REQUEST['orderby'] == 'author' && $_REQUEST['order'] == 'desc' ? 'desc' : 'asc') : 'desc');?>" style=""><a href="post_type=argolinks&orderby=author&order=<?php echo (isset($_REQUEST['orderby']) ? ($_REQUEST['orderby'] == 'author' && $_REQUEST['order'] == 'desc' ? 'asc' : 'desc') : 'desc');?>"><span>Author</span><span class="sorting-indicator"></span></a></th>
      <th scope="col" id="link-tags" class="manage-column column-link-tags" style="">Tags</th>
      <th scope="col" id="date" class="manage-column column-date <?php echo (isset($_REQUEST['orderby']) && $_REQUEST['orderby'] == 'date' ? 'sorted' : 'sortable');?> <?php echo (isset($_REQUEST['orderby']) ? ($_REQUEST['orderby'] == 'date' && $_REQUEST['order'] == 'desc' ? 'desc' : 'asc') : 'desc');?>" style=""><a href="post_type=argolinks&orderby=date&order=<?php echo (isset($_REQUEST['orderby']) ? ($_REQUEST['orderby'] == 'date' && $_REQUEST['order'] == 'desc' ? 'asc' : 'desc') : 'desc');?>"><span>Date</span><span class="sorting-indicator"></span></a></th>
    </tr>
    
    <?php $i=1; ?>
    <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
      <tr id='<?php echo get_the_ID(); ?>' class='<?php echo ($i%2 ? 'alternate' : '')?>'>
        <th scope="row" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" class='argo-link' value='<?php echo get_the_ID(); ?>'/></th>
        <td scope="row" id="title" class="manage-column column-title sortable desc" style="">
          <span id="title-<?php echo get_the_ID();?>"><?php echo the_title(); ?></span><br />
          <?php
          $custom = get_post_custom($post->ID);
          ?>
          <span id='url-<?php echo get_the_ID();?>'style='font-size:10px;'><em><?php echo (isset($custom["argo_link_url"][0]) ? $custom["argo_link_url"][0] : ''); ?></em></span>
          <span id='description-<?php echo get_the_ID();?>'style='display:none;'><em><?php echo (isset($custom["argo_link_description"][0]) ? $custom["argo_link_description"][0] : ''); ?></em></span>
        </td>
        <td scope="row" id="author" class="manage-column column-author sortable desc" style=""><span><?php the_author();?></span></td>
        <td scope="row" id="link-tags" class="manage-column column-link-tags" style="">
        <?php
        $terms = get_the_terms(get_the_ID(), 'argo-link-tags');
            if (count($terms) > 1) {
              foreach ($terms as $term) {
                echo $term->name.", ";
              }
            } else {
              echo "&nbsp;";
            }
            $terms = "";
        ?>
        </td>
        <td scope="row" id="date" class="manage-column column-date sortable asc" style=""><span><?php echo get_the_date(); ?></span></td>
      </tr>
      <?php $i++;?>
    <?php endwhile; ?>
  </table>
  <button id='append-argo-links'>Send links to editor window</button>
</div>
<?php
// Reset Query
wp_reset_query();

?>
<script type='text/javascript'>

  jQuery(function(){
    jQuery('#append-argo-links').bind('click',function(){
      jQuery('.argo-link').each(function(){
        if (jQuery(this).is(":checked")) {
          var html = "\n<p class='link-roundup'><a href='"+jQuery('#url-'+jQuery(this).val()).text()+"'>"+jQuery('#title-'+jQuery(this).val()).text()+"</a> <span class='description'>\""+jQuery('#description-'+jQuery(this).val()).text()+"\"</span></p>";
          if (jQuery('#content').is(":visible")) {
            jQuery('#content').val(jQuery('#content').val()+html);
          } else {
            parent.tinyMCE.activeEditor.setContent(parent.tinyMCE.activeEditor.getContent() + html);
          }
          
        }
        });
      return false;
      });  
  });
  jQuery('div.display-argo-links a').bind("click",function(){
    
    var urlOptions = jQuery(this).attr('href');
    jQuery('#argo-links-display-area').load('<?php echo home_url(); ?>/wp-content/plugins/argo-links/display-argo-links.php?'+urlOptions);
    return false;
  });

    


</script>