(function( $ ) {
	'use strict';

	var dataNames = [];

	jQuery(document).ready(function($)
	{
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: inputTags_ajax_object.url,
            data: "action=" + inputTags_ajax_object.action + "&nonce=" + inputTags_ajax_object.nonce,
            success: function(result)
            {
				let inputElm = document.getElementById('SuperHero_names');

				if(inputElm.value != '')
				{
					let arrayTemp = JSON.parse(inputElm.value);

					arrayTemp.forEach(function(object) {
					  dataNames.push(object.value);
					});					
				}

				let tagify = new Tagify(inputElm, {
				    enforceWhitelist: true,
				    whitelist: result
				});

				tagify.on('add', onAddTag)
				      .on('remove', onRemoveTag);
            },
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{ 
				alert("Status: " + textStatus); alert("Error: " + errorThrown); 
			}       
        });
	});

	// tag added callback
	function onAddTag(e)
	{
		dataNames.push(e.detail["data"]["value"]);

	    console.log("onAddTag: ", dataNames);

		let inputShortcode = document.getElementById('SuperHero_Shortcode');

		inputShortcode.value ="[superhero_api name='" + dataNames.join(',') +"']SUPERHEROES[/superhero_api]";
	}

	// tag remvoed callback
	function onRemoveTag(e)
	{
		let pos = dataNames.indexOf(e.detail["data"]["value"]);

		dataNames.splice(pos, 1);

	    console.log("onRemoveTag: ", dataNames);

		let inputShortcode = document.getElementById('SuperHero_Shortcode');

		inputShortcode.value ="[superhero_api name='" + dataNames.join(',') +"']SUPERHEROES[/superhero_api]";
	}
})( jQuery );

