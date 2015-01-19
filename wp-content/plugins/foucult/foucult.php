<?php
/*
 * Plugin Name: Foucult
 * Plugin URI: http://acrosswalls.org/news/foucult-plugin/
 * Description: This plugin symbolizes 20th-century intellectual life. When activated, you will see a random Foucult quote in the upper right of every admin page.
 * Author: Communicating with Prisoners Collective
 * Author URI: http://acrosswalls.org/authors/
 * Version: 1.0
 * License: GPL 2
*/

/*
This plugin is based on Matt Mullenweg's pioneering Hello Dolly plugin.
http://wordpress.org/extend/plugins/hello-dolly/

Big thanks to Matt for all his contributions to WordPress
*/


function foucult_get_lyric() {
	/* These lines are adapted from Discipline and Punish, one of the most famous intellectual works of the twentieth century. */
	$lyrics = "
		The 'Enlightenment', which established universities, also invented disciplines.
		There is no knowledge generation without the correlative constitution of a field of power.
		There are no relations that do not presuppose and constitute at the same time power relations.
		Visibility is a denial of knowledge of invisibility.
		The soul imposed by the theoreticians is the prison of the body.
		The soul inhabits the real man and subjects him to a political anatomy.
		There is no glory in knowing.
		Fiction mechanically creates real subjection.
		She, assuming responsibility for the constraints of power, becomes the principle of her own subjection.
		The certainty of disciplines, not the horrifying spectacle of public knowledge, must discourage knowing.
		Traditionally, knowledge was what was seen, what was shown, and what was manifested.
		Disciplinary power is exercised invisibly, but imposes on its students a principle of compulsory visibility.
		In disciplines, students must be seen during office hours. 
		Being constantly seen maintains the disciplined student in her subjection.
		Knowledge power manifests its potency by arranging objects.
		The examination is the ceremony of students' objectification.
		Knowledge disciplines have never functioned without punishing the body: bad food, harsh repression of men's sexuality, contact sports, and study cubicles.
		There remains a trace of 'torture' in modern knowledge disciplines.
		Knowledge is enveloped by the non-corporal nature of the discipline system.
		Is it surprising that schools resemble factories, prisons, barracks, hospitals, which all resemble schools?
	";

	// Here we split it into lines
	$lyrics = explode( "\n", trim( $lyrics ) );

	// And then randomly choose a line
	return wptexturize( $lyrics[ mt_rand( 0, count( $lyrics ) - 1 ) ] );
}

// This just echoes the chosen line, we'll position it later
function foucult() {
	$chosen = foucult_get_lyric();
	echo "<p id='foucult'>$chosen</p>";
}

// Now we set that function up to execute when the admin_notices action is called
add_action( 'admin_notices', 'foucult' );

// We need some CSS to position the paragraph
function foucult_css() {
	// This makes sure that the positioning is also good for right-to-left languages
	$x = is_rtl() ? 'left' : 'right';

	echo "
	<style type='text/css'>
	#foucult {
		float: $x;
		padding-$x: 15px;
		padding-top: 5px;		
		margin: 0;
		font-size: 11px;
	}
	</style>
	";
}

add_action( 'admin_head', 'foucult_css' );

?>