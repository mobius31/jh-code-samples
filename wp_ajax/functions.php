add_action('wp_ajax_nopriv_be_ajax_fetch_reviews', 'be_ajax_fetch_reviews');

function be_ajax_fetch_reviews() {
	
	global $wpdb;

	$source = $_POST['source'];
	$page = ( intval( $_POST['page'] ) * 10 );
	$sourceSQL = '';
	
	if ($source != '' AND $source != 'all'):
		$sourceSQL = " AND (pm.meta_key = 'source' AND pm.meta_value = '".$source."') ";
	else:
		$sourceSQL = " AND (pm.meta_key = 'source' AND pm.meta_value <> '') ";
	endif;
	
	$query = "
		SELECT 
			p.ID,
			(SELECT meta_value FROM " .$wpdb->prefix ."postmeta pm WHERE meta_key = 'review_date' AND post_id = p.ID) as review_date
		FROM " .$wpdb->prefix ."posts p 
			INNER JOIN " .$wpdb->prefix ."postmeta pm 
			ON p.ID = pm.post_id
			WHERE p.post_type = 'reviews'
			AND p.post_status = 'publish'". $sourceSQL ."
			GROUP BY p.post_title
			ORDER BY review_date DESC
			LIMIT ".$page.", 10";

	$results = $wpdb->get_results( $query );
	
	$reviews = '';
	
	foreach($results as $r):

		// -- Construct HTML from $results
		
	endforeach;	

	echo json_encode ($reviews);

	wp_die();
}
// -- End Ajax Functionality