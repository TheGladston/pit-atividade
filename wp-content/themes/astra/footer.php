<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<?php astra_content_bottom(); ?>
	</div> <!-- ast-container -->
	</div><!-- #content -->
<?php 
	astra_content_after();
		
	astra_footer_before();
		
	astra_footer();
		
	astra_footer_after(); 
?>
	</div><!-- #page -->
<?php 
	astra_body_bottom();    
	wp_footer(); 
?>

<!-- <button onclick="myFunction1()" style="color:#000; position:absolute; z-index: 99; top: 400px;"><</button>
<button onclick="myFunction2()" style="color:#000; position:absolute; z-index: 99; top: 400px; left: 65px;">></button>

<style>
	#desktop-banner2 {
		display: none;
	}
</style>

<script>
function myFunction1() {
  document.getElementById("desktop-banner1").style.display = "none";
  document.getElementById("desktop-banner2").style.display = "initial";
}
function myFunction2() {
  document.getElementById("desktop-banner2").style.display = "none";
  document.getElementById("desktop-banner1").style.display = "initial";
}
</script> -->

	</body>
</html>
