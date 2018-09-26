(() => {
	// getting all the shortcode names
	let title = document.querySelectorAll('.em-icon-name');
	if (!title) return;

	// getting search input
	let input = document.querySelector('.em-icon-input');
	if (!input) return;

	// search input listener
	input.addEventListener('input', (e) => {

		let v = e.target.value;

		// show all
		if (v.length == 0) {
			for (let c of title)
				c.parentNode.style.display = 'flex';
			
			return;
		}

		// do nothing if less than 3 chars
		if (v.length < 3) return;

		// do the search
		for (let c of title) { 
			if (c.innerHTML.indexOf(v) == -1)
				c.parentNode.style.display = 'none';
			else c.parentNode.style.display = 'flex';
		}
	});


	// container for shortcode creator
	let maker = document.querySelector('.em-icon-scmaker');
	if (!maker) return;

	// get all the svg containers
	let containerRegular = document.querySelectorAll('.em-icon-container-regular');
	let containerSolid = document.querySelectorAll('.em-icon-container-solid');
	let containerBrands = document.querySelectorAll('.em-icon-container-brands');

	// size control container
	let sizeControl = document.createElement('div');
	sizeControl.classList.add('em-icon-sizecontrol');

	// size control input
	let sizeInput = document.createElement('input');
	sizeInput.classList.add('em-icon-range');
	sizeInput.setAttribute('type', 'range');
	sizeInput.setAttribute('orient', 'vertical');
	sizeInput.setAttribute('min', '6');
	sizeInput.setAttribute('max', '256');
	sizeInput.value = 64;
	sizeInput.setAttribute('step', '1');


	// let iconSize = document.createElement('span');
	
	// size control listener
	sizeInput.addEventListener('input', (e) => {

		// updates shorcode
		fscode['size'] = ' '+e.target.value+'px';
		updateFscode();

		// gets the selected icon
		let icon = document.querySelector('.em-icon-svgc .em-svg');
		if (!icon) return;

		// changes the size of the selected icon
		icon.style.width = e.target.value+'px';
		icon.style.height = e.target.value+'px';

	});

	// adds range input to container
	sizeControl.appendChild(sizeInput);


	// color control container
	let colorContainer = document.createElement('div');

	// color control input (wp.colorpicker (iris) is added to)
	let colorControl = document.createElement('input');
	colorControl.setAttribute('type', 'text');

	// adding color control to container
	colorContainer.appendChild(colorControl);


	// shortcode text
	let fscode = {
		b: '[icon',
		name: '',
		color: '#000',
		size: '64px',
		a: ']'
	}

	// container for shortcode text
	let text = document.createElement('input');
	text.classList.add('em-icon-sctext');

	// function to update shortcode
	// updates html input node
	let updateFscode = () => {
		let s = fscode['b'];

		if (fscode['name']) s += ' '+fscode['name'];
		if (fscode['size']) s += ' '+fscode['size'];
		if (fscode['color']) s += ' '+fscode['color'];

		s += ']';

		text.value = s;
	}

	// icon container event function
	let click = (c, t) => {

		// clears shortcode creator element
		while (maker.firstChild) 
			maker.removeChild(maker.firstChild);

		maker.style.display = 'flex';

		// icon container 
		let svgContainer = document.createElement('div');
		svgContainer.classList.add('em-icon-svgc');

		// clones icon node and adds to shortcode creator
		let svg = c.querySelector('.em-svg').cloneNode(true);
		svgContainer.appendChild(svg);

		// updates shortcode text with icon name
		fscode['name'] = c.querySelector('.em-icon-name').innerHTML+t;
		updateFscode();

		// resets size control
		sizeInput.value = 64;
		fscode['size'] = '64px';

		// window.scrollTo(0, 0);

		// close button
		let button = document.createElement('button');
		button.setAttribute('type', 'button');
		button.classList.add('em-icon-button');
		button.addEventListener('click', () => maker.style.display = 'none');
		button.appendChild(document.createTextNode('close'));

		maker.appendChild(button);

		// adds cloned icon to shortcode creator
		maker.appendChild(svgContainer);

		// adds shortcode text to shortcode creator
		maker.appendChild(text);

		// adds size control to shortcode creator
		maker.appendChild(sizeControl);

		// adds color control to shortcode creator
		maker.appendChild(colorContainer);

		// gets the path tag of icon
		let p = svg.querySelector('.em-path');

		// adding and listener for color picker
		jQuery(colorControl).wpColorPicker({
			hide: false,
			width: 200, 
			change: function(event, ui) {
				// changes the cloned icon's color 
				p.setAttribute('fill', ui.color.toString());

				// updates shortcode text
				fscode['color'] = ui.color.toString();
				updateFscode();
			}});

		// sets default color
		jQuery(colorControl).iris('color', 'hsl(0, 100%, 1%)');
	}

	// adds click event listeners to icon containers
	for (let c of containerRegular)
		c.addEventListener('click', function() { click(this, '') });

	for (let c of containerSolid)
		c.addEventListener('click', function() { click(this, ' solid') });

	for (let c of containerBrands)
		c.addEventListener('click', function() { click(this, ' brand') });


})();