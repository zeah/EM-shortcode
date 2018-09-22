(() => {
	console.log("hello");
	let title = document.querySelectorAll(".em-icon-name");

	if (!title) return;

	let input = document.querySelector(".em-icon-input");

	if (!input) return;

	input.addEventListener("input", (e) => {

		let v = e.target.value;

		if (v.length == 0) {

			for (let c of title)
				c.parentNode.style.display = "flex";
			return;
		}


		if (v.length < 3) return;

		for (let c of title) { 
			if (c.innerHTML.indexOf(v) == -1)
				c.parentNode.style.display = "none";
			else c.parentNode.style.display = "flex";
		}
	});

	let maker = document.querySelector(".em-icon-scmaker");

	if (!maker) return;

	let container = document.querySelectorAll(".em-icon-container");
	if (!container) return;

	let sizeControl = document.createElement("div");

	let sizeInput = document.createElement("input");
	sizeInput.setAttribute("type", "range");

	sizeControl.appendChild(sizeInput);

	let colorContainer = document.createElement("div");
	let colorControl = document.createElement("input");
	colorControl.setAttribute('type', 'text');
	colorContainer.appendChild(colorControl);

	// jQuery(colorControl).wpColorPicker();

	for (let c of container)
		c.addEventListener("click", () => {
			while (maker.firstChild) 
				   maker.removeChild(maker.firstChild);

			let svg = c.querySelector(".em-svg").cloneNode(true);
			let text = '[icon '+c.querySelector(".em-icon-name").innerHTML+']';
			let p = svg.querySelector(".em-path");
			// p.setAttribute("fill", "#000");

			maker.appendChild(svg);

			let shortcode = document.createElement("div");
			// shortcode.appendChild(text);
			// shortcode.appendChild(document.createTextNode("[icon "+c.querySelector(".em-icon-name").innerHTML+"]"));
			// shortcode.style.textAlign = "center";
			maker.appendChild(shortcode);

			maker.appendChild(sizeControl);
			maker.appendChild(colorContainer);
			jQuery(colorControl).wpColorPicker({
				// mode: 'hsv',
    			palettes: false, 
    			hide: false,
    			width: 200, 
    			change: function(event, ui) { p.setAttribute('fill', ui.color.toString()); }});

			jQuery(colorControl).iris('color', 'hsl(0, 100%, 1%)');
		});


})();