<?php
/*
Plugin Name: QQWorld Content Provider
Plugin URI: https://wordpress.org/plugins/qqworld-content-provider/
Description: Using for Mobile APP.
Version: 1.0
Author: Michael Wang
Author URI: http://www.qqworld.org
Text Domain: qqworld-content-provider
*/

define('QQWORLD_CONTENT_PROVIDER_DIR', __DIR__);
define('QQWORLD_CONTENT_PROVIDER_URL', plugin_dir_url(__FILE__));

class qqworld_content_provider {
	var $text_domain = 'qqworld-content-provider';
	public function __construct() {
		add_action( 'plugins_loaded', array($this, 'load_language'), 1 );
		add_action( 'wp_ajax_qqworld_content_provider', array($this, 'content_provider') );
		add_action( 'wp_ajax_nopriv_qqworld_content_provider', array($this, 'content_provider') );
	}

	public function load_language() {
		load_plugin_textdomain( $this->text_domain, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	public function outside_language() { // for poedit
		__('Michael Wang', $this->text_domain);
		__('QQWorld Content Provider', $this->text_domain);
		__('Using for Mobile APP.', $this->text_domain);
	}

	public function content_provider() {
		global $wp_query, $post;
		
		$query = $_REQUEST['query'];
		query_posts($query);

		/* paged */
		$paged = max( 1, get_query_var('paged') );
		$numpages = $wp_query->max_num_pages;
		header ("Content-Type:text/xml");
		echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<contentProvider
	xmlns:bloginfo="http://www.qqworld.org/content-provider/bloginfo/"
	xmlns:additional="http://www.qqworld.org/content-provider/additional/">
	<site>
		<bloginfo:name><![CDATA[<?php bloginfo('name'); ?>]]></bloginfo:name>
		<bloginfo:description><![CDATA[<?php bloginfo('description'); ?>]]></bloginfo:description>
		<bloginfo:wpurl><![CDATA[<?php bloginfo('wpurl'); ?>]]></bloginfo:wpurl>
		<bloginfo:url><![CDATA[<?php bloginfo('url'); ?>]]></bloginfo:url>
		<bloginfo:admin_email><![CDATA[<?php bloginfo('admin_email'); ?>]]></bloginfo:admin_email>
		<bloginfo:charset><![CDATA[<?php bloginfo('charset'); ?>]]></bloginfo:charset>
		<bloginfo:version><![CDATA[<?php bloginfo('version'); ?>]]></bloginfo:version>
		<bloginfo:html_type><![CDATA[<?php bloginfo('html_type'); ?>]]></bloginfo:html_type>
		<bloginfo:text_direction><![CDATA[<?php bloginfo('text_direction'); ?>]]></bloginfo:text_direction>
		<bloginfo:language><![CDATA[<?php bloginfo('language'); ?>]]></bloginfo:language>
		<bloginfo:stylesheet_url><![CDATA[<?php bloginfo('stylesheet_url'); ?>]]></bloginfo:stylesheet_url>
		<bloginfo:stylesheet_directory><![CDATA[<?php bloginfo('stylesheet_directory'); ?>]]></bloginfo:stylesheet_directory>
		<bloginfo:template_url><![CDATA[<?php bloginfo('template_url'); ?>]]></bloginfo:template_url>
		<bloginfo:template_directory><![CDATA[<?php bloginfo('template_directory'); ?>]]></bloginfo:template_directory>
		<bloginfo:pingback_url><![CDATA[<?php bloginfo('pingback_url'); ?>]]></bloginfo:pingback_url>
		<bloginfo:atom_url><![CDATA[<?php bloginfo('atom_url'); ?>]]></bloginfo:atom_url>
		<bloginfo:rdf_url><![CDATA[<?php bloginfo('rdf_url'); ?>]]></bloginfo:rdf_url>
		<bloginfo:rss_url><![CDATA[<?php bloginfo('rss_url'); ?>]]></bloginfo:rss_url>
		<bloginfo:rss2_url><![CDATA[<?php bloginfo('rss2_url'); ?>]]></bloginfo:rss2_url>
		<bloginfo:comments_atom_url><![CDATA[<?php bloginfo('comments_atom_url'); ?>]]></bloginfo:comments_atom_url>
		<bloginfo:comments_rss2_url><![CDATA[<?php bloginfo('comments_rss2_url'); ?>]]></bloginfo:comments_rss2_url>
	</site>
	<additional>
		<additional:paged><![CDATA[<?php echo $paged; ?>]]></additional:paged>
		<additional:numpages><![CDATA[<?php echo $numpages; ?>]]></additional:numpages>
	</additional>
<?php
		/*echo '<pre>';
		print_r($wp_query);
		echo '</pre>';*/

		if ( have_posts() ) :
			echo "<articles>";
			while ( have_posts() ) : the_post();
?>
		<article>
			<ID><![CDATA[<?php the_ID(); ?>]]></ID>
			<title><![CDATA[<?php the_title(); ?>]]></title>
			<excerpt><![CDATA[<?php the_excerpt(); ?>]]></excerpt>
			<content><![CDATA[<?php the_content(); ?>]]></content>
			<time><![CDATA[<?php the_time("Y-m-d H:i:s") ?>]]></time>
			<?php
			if (has_post_thumbnail()) :
				$thumbnail_id = get_post_thumbnail_id($post->ID);
			?>
			<featured-image id="<?php echo $thumbnail_id; ?>">
				<thumbnail><![CDATA[<?php $image = wp_get_attachment_image_src($thumbnail_id, 'thumbnail'); echo $image[0]; ?>]]></thumbnail>
				<miduim><![CDATA[<?php $image = wp_get_attachment_image_src($thumbnail_id, 'medium'); echo $image[0]; ?>]]></miduim>
				<large><![CDATA[<?php $image = wp_get_attachment_image_src($thumbnail_id, 'large'); echo $image[0]; ?>]]></large>
				<full><![CDATA[<?php $image = wp_get_attachment_image_src($thumbnail_id, 'full'); echo $image[0]; ?>]]></full>
			</featured-image>
			<?php endif; ?>
			<taxonomies>
<?php
				$taxonomies = get_object_taxonomies($post);
					foreach ($taxonomies as $taxonomy) :
						if ($taxonomy == 'post_format') continue;
						$terms = get_the_terms( get_the_ID(), $taxonomy );
?>
			
				<taxonomy name="<?php echo $taxonomy; ?>">
					<?php foreach ($terms as $id => $term) : ?>
					<term id="<?php echo $id; ?>" slug="<?php echo $term->slug; ?>" count="<?php echo $term->count; ?>"><![CDATA[<?php echo $term->name;?>]]></term>
					<?php endforeach; ?>
				</taxonomy>
<?php
					endforeach;
?>
			</taxonomies>
		</article>
<?php
			endwhile;
			echo "</articles>";
		endif;
		wp_reset_query();
		echo '</ccontentProvider>';
		exit;
	}
}
new qqworld_content_provider;
?>