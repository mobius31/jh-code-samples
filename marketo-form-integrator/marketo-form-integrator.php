<?php
	/*
	Plugin Name: Marketo Forms 2.0 Integrator
	Plugin URI: http://www.fullmotionstudio.com
	Description: Allows Marketo's Forms2.0 to be generated via Shortcode.
	Version: 0.1
	Author: Jon Houston
	Author URI: http://www.fullmotionstudio.com
	License: Private
	*/
	
	add_shortcode( 'm2form', 'mf2i_gen_form_handler' );
	
	function mf2i_gen_form_handler( $atts ) {
		
		$html = '';
		if ($atts['debug'] == 'true'):
			$html .= 'Debug: <br><div id="mkt-debug"></div>';
		endif;
		$html .= '<script src="//app-sj01.marketo.com/js/forms2/js/forms2.min.js"></script>';
		$html .= '<form id="mktoForm_'.$atts['formid'].'"></form>';
		
		new mf2i($atts['campaign'], $atts['formid']);
		
		return $html;
		
	}
	
	class mf2i {
		
		private $campaign_id;
		private $form_id;
		
		public function __construct( $campaign_id, $form_id ) {
			
			$this->campaign_id = $campaign_id;
			$this->form_id = $form_id;
			
			add_action( 'wp_footer', function () { $this->mf2i_footer(); }, 100 );
		}
		
		/**
		 * Function: mf2i_footer | returns null
		 * Description: Adds custom script to wp_footer() to integrate custom data to form fields
		 */
		private function mf2i_footer() {
			
			echo '<script>MktoForms2.loadForm("//app-sj01.marketo.com", "'.$this->campaign_id.'", '.$this->form_id.', function(form) {
			
						var mfi = {
							init: function () {
								
								mfi.ajaxSubmitURLParams();
								
							},
							ajaxSubmitURLParams: function () {
								
								var protocol = "http:";
								var response;
								
								if(window.location.protocol == "https:") {
									protocol = "https:";
								}
						
								var postAjax = {"ajaxurl":protocol+"\/\/"+window.location.host+"\/wp-admin\/admin-ajax.php"};
								
								var postData = { 
									\'action\': \'upts_set_params_to_session\',
									\'urlparams\' : mfi.fetchURLParametersAsString()
								};
						
								jQuery.ajax({
									url: postAjax.ajaxurl,
									type: "POST",
									data: postData,
									success: function ( data ) {
										
										//console.log(data);
										response = jQuery.parseJSON(data);
										
										jQuery.each(response, function (key, data) {
											console.log(key);
												console.log(data);
												
												jQuery("#mktoForm_'.$this->form_id.'").find(\'[name="\'+key+\'"]\').attr(\'value\', data);
												form.setValues({
													key : data
												});
										});
										/* form.setValues({
											\'Lead_Source__c\': \'Test\'
										}); */
										
										console.log(form.getValues());
										
									}
								});
								
								return response;
							},
							fetchURLParametersAsArray: function () {
								
								var params = [], hash;
								var hashes = window.location.href.slice(window.location.href.indexOf(\'?\') + 1).split(\'&\');
								
								for(var i = 0; i < hashes.length; i++)
								{
										hash = hashes[i].split(\'=\');
										params.push(hash[0]);
										params[hash[0]] = hash[1];
								}
								
								return params;
								
							},
							fetchURLParametersAsString: function () {
								
								var hashes = window.location.href.slice(window.location.href.indexOf(\'?\') + 1);
								
								return hashes;
								
							}
						};
						$(mfi.init());
			
		
			
			
		});</script>';
			
		}
		
	}