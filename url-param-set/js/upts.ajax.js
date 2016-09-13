// JavaScript Document

var URLParamsToSessions = {
	init: function () {
		
		URLParamsToSessions.ajaxSubmitURLParams();

		
	},
	ajaxSubmitURLParams: function () {
		
		var protocol = "http:";
		var response;
		
		//console.log(source);
		
		if(window.location.protocol == "https:") {
			protocol = "https:";
		}

		var postAjax = {"ajaxurl":protocol+"\/\/"+window.location.host+"\/wp-admin\/admin-ajax.php"};
		
		var postData = { 
			'action': 'upts_set_params_to_session',
			'urlparams' : URLParamsToSessions.fetchURLParametersAsString()
		};
		
		//console.log(postData);

		jQuery.ajax({
			url: postAjax.ajaxurl,
			type: "POST",
			data: postData,
			success: function ( data ) {
				
				//console.log(data);
				response = data;
				$('#mkt-debug').html(jQuery.parseJSON(data).debug_string);
				
			}
		});
		
		return response;
	},
	fetchURLParametersAsArray: function () {
		
		var params = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        params.push(hash[0]);
        params[hash[0]] = hash[1];
    }
		
    return params;
		
	},
	fetchURLParametersAsString: function () {
		
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1);
		
    return hashes;
		
	}
};
$(URLParamsToSessions.init());