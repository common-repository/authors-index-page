<?php   
	/* 
	Plugin Name: Authors Index Page 
	Plugin URI: http://www.kune.fr 
	Description: Plugin for displaying an index of the authors 
	Author: Mat_
	Version: 1.0 
	Author URI: http://www.kune-studio.com 
	*/  
?>

<?php 
function authorsIndex_init($atts) {

	function getLastArticle($author){
		//echo $author;
		global $wpdb, $post;
		$pre = $wpdb->prefix;
		global $domain;
		//echo $author;
		$author_id = $wpdb->get_results("SELECT ID, display_name FROM wp_users WHERE display_name = \"{$author}\" LIMIT 1");
		$ID = $author_id[0]->ID; 
		//echo var_dump($author_id);
		$last_posts = $wpdb->get_results("
			SELECT ID, post_title, post_date, post_author
			FROM {$pre}posts
			WHERE post_author = \"{$ID}\"
			LIMIT 5
		");

		$liste = "<div style=\"display: none;\" class=\"iapUldiv\" ><ul class=\"iapListPosts\">";
		//$liste .= var_dump($last_posts);
		foreach($last_posts as $thepost){
			$liste .= "<li><a class=\"iapUllink\" href=\"".get_permalink($thepost->ID)."\">".$thepost->post_title."</a></li>";	
		}
		$liste .= "</ul></div>";
		return $liste;

	}

	function authorsIndexDisplay_function($atts) {

		extract(shortcode_atts(array(
			'ul' => 'iapUl',
			'li' => 'iapIl',
			'letter' => 'iapLetter',
			'hide_empty' => false,
			'optioncount' => true,
			'show_fullname' => false,
			'exclude_admin' => false,
			'listpost' => true,
			'menu' => true
		), $atts));

		$defaults = array(
		  'optioncount' => $option_count, 
		  'exclude_admin' => $exclude_admin, 
		  'show_fullname' => $show_fullname,
		  'hide_empty' => $hide_empty,
		  'echo' => false,
		  'menu' => $menu
		 );

		$ret = '';
		$tag = substr(wp_list_authors($defaults),4);
		$tag = explode("</li><li>",$tag);

		$alpha = array();
		$i = 0;
		$start = "0";

		$ul = " class=\"$ul\" ";
		$li = " class=\"$li\" ";

		foreach($tag as $untag){

			if(ereg(">([A-Za-z0-9\.|-|_È‡ËÍÁ ]*)</a>",$untag, $letag)){

				$letag[1] = ucfirst($letag[1]);

				if($start == "0"){
					$start = $letag[1][0];
					$alpha[$i] = $start;
					$i ++;
					$letter2 = " class=\"$letter\" id=\"iap".ucfirst($start)."\"";
					$ret .= "<span ".$letter2.">".$start."</span>";
					$ret .= "<ul $ul>";
				}
				if($letag[1][0] == $start){
					$ret .= "\t<li ".$li.">$untag<br/>";
					if($listpost) {$ret .= getLastArticle($letag[1]);}
					$ret .= "</li>\n";
					
					}
				else{
					$ret .= "</ul>\n";
					$start = $letag[1][0];
					$alpha[$i] = $start;
					$i ++;
					$letter2 = " class=\"$letter\" id=\"iap".ucfirst($start)."\"";
					$ret .= "<span ".$letter2.">".$start."</span>";
					$ret .= "<ul $ul>\n";
					$ret .= "\t<li>$untag<br/>";
					if($listpost) {$ret .= getLastArticle($letag[1]);}
					$ret .= "</li>\n";
				}
			}
			else{

				$untag = ucfirst($untag);

				if($start == "0"){
					$start = $untag[0];
					$alpha[$i] = $start;
					$i ++;
					$letter2 = " class=\"$letter\" id=\"iap".$start."\"";
					$ret .= "<span ".$letter2.">".$start."</span>\n";
					$ret .= "<ul $ul>";
				}
				if($untag[0] == $start){
					$ret .= "\t<li ".$li.">$untag<br/>";
					if($listpost) { $ret .= getLastArticle($untag); }
					$ret .= "</li>\n";
					}
				else{
					$ret .= "</ul>\n";
					$start = $untag[0];
					$alpha[$i] = $start;
					$i ++;
					$letter2 = " class=\"$letter\" id=\"iap".ucfirst($start)."\"";
					$ret .= "<span ".$letter2.">".$start."</span>";
					$ret .= "<ul $ul>\n";
					$ret .= "\t<li>$untag<br/>";
					if($listpost) { $ret .= getLastArticle($untag); }
					$ret .= "</li>\n";
				}

			}
		}
		if($menu){
			$retMenu = "<ul id=\"iapAlpha\">\n";
			foreach($alpha as $alphabet){
				$retMenu .= "\t<li><a class=\"iapUllink\" href=\"#iap".$alphabet."\">".$alphabet."</a></li>\n";
			}
			$retMenu .= "</ul>";
		}
		$ret .= "</div>";
		$retMenu .= $ret;
		$retMenu .= '<div id="index-authors-page">';

		return $retMenu;
	} 

	add_shortcode('authorsindex', 'authorsIndexDisplay_function');
}
function authorsiindexpage_insert_css()
{
echo '<link rel="stylesheet" href="'.get_option('siteurl').'/wp-content/plugins/authors-index-page/authorindex.css" type="text/css" media="screen" />'."\n";
echo '<link rel="stylesheet" href="'.get_option('siteurl').'/wp-content/plugins/authors-index-page/js/jquery.tooltip.css" type="text/css" media="screen" />'."\n";
}
if ( !is_admin() )
{
	wp_enqueue_script('jquery');
	wp_enqueue_script('tdim','/wp-content/plugins/authors-index-page/js/jquery.dimensions.js');
	wp_enqueue_script('tTip','/wp-content/plugins/authors-index-page/js/jquery.tooltip.min.js');

}
function authorsiindexpage_insert_js ()
{
	
	echo '';
	?>

<script type="text/javascript">
//<![CDATA[
	

	jQuery(document).ready(function() {
		//jQuery(".toto").hide();
		jQuery(".iapUl a").tooltip({ 
			bodyHandler: function() { 
				//alert(jQuery(this).parent().children("div.iapUldiv").html());
				if(jQuery(this).parent().children("div.iapUldiv").length > 0){
				return jQuery(this).parent().children("div.iapUldiv").html();}
				else return "Visitez la page de l'auteur!"; 
			}, 
			showURL: false 
		});
		
	});
	
//]]>
</script>
<?php
}

add_action('wp_head', 'authorsiindexpage_insert_js');
add_action('wp_head', 'authorsiindexpage_insert_css');
add_action('plugins_loaded', 'authorsIndex_init');

?>