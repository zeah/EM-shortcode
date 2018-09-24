(() => {
	let title = document.querySelectorAll('.em-icon-name');

	if (!title) return;

	let input = document.querySelector('.em-icon-input');

	if (!input) return;

	input.addEventListener('input', (e) => {

		let v = e.target.value;

		if (v.length == 0) {

			for (let c of title)
				c.parentNode.style.display = 'flex';
			return;
		}


		if (v.length < 3) return;

		for (let c of title) { 
			if (c.innerHTML.indexOf(v) == -1)
				c.parentNode.style.display = 'none';
			else c.parentNode.style.display = 'flex';
		}
	});

	let maker = document.querySelector('.em-icon-scmaker');

	if (!maker) return;

	let container = document.querySelectorAll('.em-icon-container');
	if (!container) return;

	let sizeControl = document.createElement('div');

	let sizeInput = document.createElement('input');
	sizeInput.classList.add('em-icon-range');
	sizeInput.setAttribute('type', 'range');
	sizeInput.setAttribute('orient', 'vertical');
	sizeInput.setAttribute('min', '6');
	sizeInput.setAttribute('max', '128');
	sizeInput.setAttribute('value', '64');
	sizeInput.setAttribute('step', '1');

	let iconSize = document.createElement('span');
	
	sizeInput.addEventListener('input', (e) => {

		iconSize.innerHTML = ' '+e.target.value+'px';

		let icon = document.querySelector('.em-icon-svgc .em-svg');

		if (!icon) return;

		icon.style.width = e.target.value+'px';
		icon.style.height = e.target.value+'px';

	});

	sizeControl.appendChild(sizeInput);

	let colorContainer = document.createElement('div');
	let colorControl = document.createElement('input');
	colorControl.setAttribute('type', 'text');
	colorContainer.appendChild(colorControl);

	// jQuery(colorControl).wpColorPicker();

	for (let c of container)
		c.addEventListener('click', () => {
			while (maker.firstChild) 
				   maker.removeChild(maker.firstChild);

			let svgContainer = document.createElement('div');
			svgContainer.classList.add('em-icon-svgc');
			let svg = c.querySelector('.em-svg').cloneNode(true);
			svgContainer.appendChild(svg);


			let iconName = document.createElement('span');
			iconName.appendChild(document.createTextNode(' '+c.querySelector('.em-icon-name').innerHTML));
			let iconColor = document.createElement('span');

			let text = document.createElement('div');
			text.appendChild(document.createTextNode('[icon'));
			text.appendChild(iconName);
			text.appendChild(iconSize);
			text.appendChild(iconColor);
			text.appendChild(document.createTextNode(']'));

			let p = svg.querySelector('.em-path');
			// p.setAttribute('fill', '#000');

			sizeInput.setAttribute('value', '64');
			iconSize.innerHTML = '';

			maker.appendChild(svgContainer);

			let shortcode = document.createElement('div');
			shortcode.classList.add('em-icon-shortcode');
			shortcode.appendChild(text);
			// shortcode.innerHTML = text;
			// shortcode.appendChild(document.createTextNode('[icon '+c.querySelector('.em-icon-name').innerHTML+']'));
			// shortcode.style.textAlign = 'center';
			maker.appendChild(shortcode);

			maker.appendChild(sizeControl);
			maker.appendChild(colorContainer);
			jQuery(colorControl).wpColorPicker({
				// mode: 'hsv',
    			palettes: false, 
    			hide: false,
    			width: 200, 
    			change: function(event, ui) { 
    				p.setAttribute('fill', ui.color.toString()); 
    				iconColor.innerHTML = ' '+ui.color.toString();
    			}});

			jQuery(colorControl).iris('color', 'hsl(0, 100%, 1%)');
		});


})();