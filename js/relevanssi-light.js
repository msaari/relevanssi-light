jQuery(document).ready(function ($) {
	$("#process").click(function () {
		console.log("Getting chunks")
		var data = {
			action: "relevanssi_light_get_chunks",
			security: nonce.relevanssi_light_process_nonce,
		}
		jQuery.post(ajaxurl, data, function (response) {
			chunks = JSON.parse(response)
			console.log("Received chunks")
			var totalChunks = 0
			for (i of chunks) {
				totalChunks += i.length
			}
			var progressMeter = $("#relevanssi_light_process")
			progressMeter.attr("max", totalChunks)
			progressMeter.show()

			process_chunks(chunks)
		})
	})
})

function process_chunks(chunks) {
	console.log("Processing chunk")
	var chunk = chunks.shift()
	if ( ! chunk ) {
		console.log("No chunk received!");
		return
	}
	var data = {
		action: "relevanssi_light_process_chunks",
		chunk: chunk,
		security: nonce.relevanssi_light_process_nonce,
	}
	jQuery.post(ajaxurl, data, function (response) {
		var progressMeter = jQuery("#relevanssi_light_process")

		var currentValue = progressMeter.attr("value")
		if (currentValue == undefined) currentValue = 0
		var newValue = currentValue + chunk.length

		progressMeter.attr("value", newValue)
		progressMeter.html(`${newValue} posts done`)

		if (chunks.length > 0) process_chunks(chunks)
	})
}
