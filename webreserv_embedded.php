<?php

/*
Plugin Name: WebReserv Embedded Booking Calendar
Plugin URI: http://blog.webreserv.eu/webreserv-booking-plugins-for-wordpress/
Description: The WebReserv Embedded Booking Calendar plugin lets you embed the WebReserv booking component directly in any PAGE or POST. The installation includes the code for a demo account so you can see how it works without a WebReserv account. Works for nearly any type of business, RV or Car Rentals, property rentals, B&B, meeting rooms etc. Remember to create a free WebReserv account to try it out with your bookable product. 
Version: 1.6
Author: WebReserv	
Author URI: http://blog.webreserv.eu/webreserv-booking-plugins-for-wordpress/
*/

// Set The Path to the plugin
define('WEBRESERVPATH', get_option('siteurl').'/wp-content/plugins/webreserv-embedded-booking-calendar');
// Set Installed flags and priveliges
$webreserv_installed = true;
$webreserv_privileges = 0;

// Define the default iFrame
$wriframe = "<iframe src=http://www.webreserv.eu/services/bookonline.do?businessid=bobsboatdemogb&embedded=y&list=n width=700px height=500px scrolling=auto frameborder=0></iframe>";

// Initialize
add_action('init', 'webreserv_calendar_init');
add_action('widgets_init', 'widget_init_webreserv');
add_filter('the_content','webreserv_insert');




// Insertion Function for POSTs and PAGEs
function webreserv_insert($content)
{
  if (preg_match('{WEBRESERV}',$content))
    {
    $content = str_replace('{WEBRESERV}',webreserv(),$content);
    }
  return $content;
}



function webreserv()
{
   global  $userdata, $table_prefix, $wpdb, $webreserv_installed;
   get_currentuserinfo();
   $str='';
  if( !webreserv_calendar_installed() )

		$webreserv_installed = webreserv_calendar_install();

    if( !$webreserv_installed )

    {

		echo "Plugin not installed correctly";

		return;

	}

	    $query = "

			SELECT code AS code

			FROM ".$table_prefix."webreserv_calendar	LIMIT 1		";

		//echo $query;

		$code = $wpdb->get_var( $query );

		

	//}

	?>

	

	<?php

    $str.='<div class="wrap">';

	if( $code === null )

	{

		$str.= '<h4>You don\'t have webreserv Calendar, please set code in Settings menu.</h4>';

	}

	else

	{

		

				

		$str.='<center>';

		

		$str.='<div id="CalendarDiv">';

		?><?php

		 $str.= $code;

		  ?>

		<?php 

		$str.='</div>';

		 

		 

		$str.='</center>';

	 

	}

	?>

	<?php

    $str.='</div>';

	

	return $str;

	

	

}



function widget_init_webreserv() {

  if (!function_exists('register_sidebar_widget'))

  	return;

 // register_sidebar_widget('WebReserv2','widget_calendar_webreserv');

}



function widget_calendar_webreserv() {

	echo "webreserv";

	}



function webreserv_calendar_init()

{		

	global $webreserv_privileges, $table_prefix, $wpdb, $webreserv_path, $webreserv_default, $webreserv_installed;

 	add_action('admin_menu', 'webreserv_calendar_config_page');

}



function webreserv_calendar_config_page() 

{



	if ( function_exists('add_submenu_page') )

	{

		add_menu_page('webreserv Calendar', 'WebReserv Booking Calendar', 8, __FILE__, 'webreserv_calendar_main_page');

		//add_submenu_page(__FILE__, 'Settings', 'Settings', $webreserv_privileges, 'maintenance', 'webreserv_calendar_manage_page');

		//add_submenu_page(__FILE__, 'Admin Settings', 'Admin Settings', 8, 'admin_maitenance', 'webreserv_calendar_admin_manage_page');

	}

}



function webreserv_calendar_main_page()

{

	global $webreserv_default, $userdata, $table_prefix, $wpdb, $webreserv_installed, $wriframe;

    get_currentuserinfo();

    

    if( !webreserv_calendar_installed() )

		$webreserv_installed = webreserv_calendar_install();

	

    if( !$webreserv_installed )

    {

		echo "PLUGIN NOT CORRECTLY INSTALLED, PLEASE CHECK ALL INSTALL PROCEDURE!";

		return;

	}

	?>

	<div class="wrap">

	<?php

	$valid = true;



	$queryS = "select * from ".$table_prefix."webreserv_calendar limit 1";


	$d1 = $wpdb->get_var( $queryS );


	if( $d1 === null )

		{

			$query ="

				INSERT INTO ".$table_prefix."webreserv_calendar (code)

				VALUES ('". $wriframe ."')

			";

			$wpdb->query( $query );

		}

	else

		{

			$query = "SELECT code AS code FROM ".$table_prefix."webreserv_calendar	LIMIT 1";

			$wriframe = $wpdb->get_var( $query );

		}

	

		

	if( isset($_POST["set"]) AND $_POST["set"] == "SAVE" )

	{

			

		if( !webreserv_calendar_code( $_POST["code"] ) )

			$valid = false;

		else

		

		{

			$query ="Update ".$table_prefix."webreserv_calendar set code = '".$_POST["code"]."'";
			// where calendar_id = " & $d1 ->calendar_id;



			$wpdb->query( $query );

			$wriframe = str_replace("\\", "", ($_POST["code"]));

		}

	}

	

	if( isset( $_GET["ui"]) and $_GET["ui"] == "true" )

	{

		$query = "

			DROP TABLE ".$table_prefix."webreserv_calendar

		";

		mysql_query( $query ) or die( mysql_error() );

		

		delete_option( 'webreserv_calendar_privileges' ); //Removing option from database...

		

		$installed = webreserv_calendar_installed();

		

		if( !$installed ) {

			echo "PLUGIN UNINSTALLED. NOW DE-ACTIVATE PLUGIN.<br />";

			echo " <a href=plugins.php>CLICK HERE</a>";

			return;

			}

		else

		{

			echo "PROBLEMS WITH UNINSTALL FUNCTION.";

		}

			

	}

	?>



	<div style="margin-bottom:20px;"><h2>WebReserv Embedded Booking Calendar</h2></div>

	<div>

	<div style="float:left;">
	<b>Example Booking Component Screenshots</b><br>
	<a href="http://tasks.webreserv.eu/webreserv_screenshots/show_large_screenshot.html?image=accomodation_9_large.PNG" target="_blank"><img src="<?php echo WEBRESERVPATH; ?>/accomodation_9_small.PNG" border="0" /></a>&nbsp;&nbsp;
     	<a href="http://tasks.webreserv.eu/webreserv_screenshots/show_large_screenshot.html?image=accomodation_5_large.PNG" target="_blank"><img src="<?php echo WEBRESERVPATH; ?>/accomodation_5_small.PNG" border="0" /></a>&nbsp;&nbsp;
	<a href="http://tasks.webreserv.eu/webreserv_screenshots/show_large_screenshot.html?image=boat_hire_3_large.PNG" target="_blank"><img src="<?php echo WEBRESERVPATH; ?>/boat_hire_3_small.PNG" border="0" /></a><br>
	<br>
	<b>Back-Office Screenshots</b><br>
	<a href="http://tasks.webreserv.eu/webreserv_screenshots/show_large_screenshot.html?image=navigation_1_large.PNG" target="_blank"><img src="<?php echo WEBRESERVPATH; ?>/navigation_1_small.PNG" border="0" /></a>&nbsp;&nbsp;
     	<a href="http://tasks.webreserv.eu/webreserv_screenshots/show_large_screenshot.html?image=bookings_overview_1_large.PNG" target="_blank"><img src="<?php echo WEBRESERVPATH; ?>/bookings_overview_1_small.PNG" border="0" /></a>&nbsp;&nbsp;
	<a href="http://tasks.webreserv.eu/webreserv_screenshots/show_large_screenshot.html?image=bookings_overview_2_large.PNG" target="_blank"><img src="<?php echo WEBRESERVPATH; ?>/bookings_overview_2_small.PNG" border="0" /></a><br>
	<br>
	<a href="http://tasks.webreserv.eu/webreserv_screenshots/show_large_screenshot.html?image=reports_1_large.PNG" target="_blank"><img src="<?php echo WEBRESERVPATH; ?>/reports_1_small.PNG" border="0" /></a>&nbsp;&nbsp;
     	<a href="http://tasks.webreserv.eu/webreserv_screenshots/show_large_screenshot.html?image=widget_1_large.PNG" target="_blank"><img src="<?php echo WEBRESERVPATH; ?>/widget_1_small.PNG" border="0" /></a>&nbsp;&nbsp;
	<a href="http://tasks.webreserv.eu/webreserv_screenshots/show_large_screenshot.html?image=help_1_large.PNG" target="_blank"><img src="<?php echo WEBRESERVPATH; ?>/help_1_small.PNG" border="0" /></a>


	<br />


	<span style="float:left;width:400px;padding-left:0px;">
<hr>
<strong>Sign Up for a WebReserv Account</strong><br>
<p style="font-size:10px;">Create a FREE account on either WebReserv .EU or .COM</p>
<b>Sign Up for a WebReserv.EU Account</b><br>
<p style="font-size:10px;">If your business is located in Europe (Not just EU, but any country in Europe), then you can sign up for a WebReserv.EU account.<br>
<a href ="http://www.webreserv.eu/signup.do" target="_new">Click here to create a <b>.EU</b> account</a></p>
<b>Sign Up for a WebReserv.COM Account</b><br>
<p style="font-size:10px;">If your business is located in any other country in the world, then you can sign up for a WebReserv.COM account.<br>
<a href ="http://www.webreserv.com/signup.do?referralID=x1013" target="_new">Click here to create a <b>.COM</b> account</a></p>

	<br />

	</span>
</div>	

	

	<div style="float:left;width:400px;padding-left:20px;" >

    <form action="<?php echo $_SERVER["PHP_SELF"]."?page=".$_GET["page"]; ?>" method="POST">

	<b>Steps to Set-up the Component</b><br><br>
 	<b>1 - Enter your WebReserv Calendar Code</b><br />
<p style="font-size:10px;">Paste the code in from the WebReserv Back Office.<br>
	<a href="http://blog.webreserv.eu/how-to-create-the-webreserv-code-in-the-webreserv-back-office-for-the/" Target="_new">How to create the WebReserv code in the WebReserv Back Office for the WebReserv Embedded Booking Calendar.</a><br>
	<i>Remember to press SAVE.</i>
        <textarea type ="text" name="code" rows="7" cols="60"><?php echo $wriframe ?></textarea></p>
        <input type="submit" name="set" value="SAVE" /></form><br>
	<b>2 - Add the {WEBRESERV} code. </b><br />
<p style="font-size:10px;">Now insert the code <b>{WEBRESERV}</b> on any POST or PAGE.<br>
	Remember you can set the height and width in the code above.</p>	
<hr>
	<b>Now use the Back Office to Manage Bookings</b><br />
<p style="font-size:10px;">Your Wordpress website is now configured with the WebReserv Booking Component.<br>
	Log into the WebReserv Back Office to :<br>
	 - Configure bookable Products<br>
	 - Set-up rates<br>
	 - Manage Bookings (Both online and manual)<br>
	 - See Reports<br>
	 - Etc<br>
	  <br /></p>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href ="http://www.webreserv.eu/login.do" target="_blank" class="button">&nbsp;&nbsp;Log Into WebReserv.EU Back Office&nbsp;&nbsp;&nbsp;&nbsp;	</a><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href ="http://www.webreserv.com/login.do" target="_blank" class="button">&nbsp;&nbsp;Log Into WebReserv.COM Back Office&nbsp;&nbsp;</a>


	<p><br />


	  <br />

	  <br />

	

	</p>

	</div>

	</div>

	<div>

	<div style="clear:both"></div>

	

	<?php



}



function webreserv_calendar_code( $code )

{

	if( strpos($code, "<iframe") === FALSE )

		return false;

	else

		return true;

}



function wr_get_admin_url()

{

 global $wriframe;

 $adminURL = preg_match("/http:\/\/(.*).com/", $wriframe, $matches);

 if ($adminURL = true)

 {

 $adminURL = htmlentities($matches['0']);

 $adminURL = $adminURL .'/admin';

 }

 return $adminURL;

}





function webreserv_calendar_installed()

{

	global $table_prefix, $wpdb;

	

	$query = "

		SHOW TABLES LIKE '".$table_prefix."webreserv_calendar'

	";

	
	$install = $wpdb->get_var( $query );


	

	if( $install === NULL )

		return false;

	else

		return true;

}



function webreserv_calendar_install()

{

	global $table_prefix, $wpdb;

	

	$query = "

		CREATE TABLE ".$table_prefix."webreserv_calendar (

			calendar_id INT(11) NOT NULL auto_increment,

			code TEXT NOT NULL,

			PRIMARY KEY( calendar_id )

		)

	";

	$wpdb->query( $query );



	//Using option for webreserv calendar plugin!

	add_option( "webreserv_calendar_privileges", "2" );

	

	if( !webreserv_calendar_installed() )

		return false;

	else

		return true;

}







?>
