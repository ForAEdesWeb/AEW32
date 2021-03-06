/*
---
description:     
  - MultiSelect is a MooTools plugin that turns your checkbox set into one single multi-select dropdown menu. MultiSelect is also completely CSS skinnable.

authors:
  - Blaž Maležič (http://twitter.com/blazmalezic)

version:
  - 1.3.1

license:
  - MIT-style license

requires:
  core/1.2.1:   '*'

provides:
  - MultiSelect
...
*/
var MultiSelect = new Class({
	
	Implements: [Options], 
	
	options: {
		boxes: 'input[type=checkbox]', 	// checkbox selector
		labels: 'label', 				// label selector
		monitorText: ' selected',		// monitor text (localization)
		containerClass: 'MultiSelect', 	// element container CSS class
		monitorClass: 'monitor',		// monitor CSS class
		monitorActiveClass: 'active',	// monitor open CSS class
		itemSelectedClass: 'selected',	// list item selected CSS class
		itemHoverClass: 'hover'			// list item hover CSS class - usually we would use CSS :hover pseudo class, but we need this for keyboard navigation functionality
	}, 
	
	initialize: function(selector, options) {
		// set options
		this.setOptions(options);
		
		// set global action variables
		this.active = false;
		this.action = 'open';
		this.state = 'closed';
		
		// get elements array
		this.elements = document.getElements(selector);
		
		// off we go...
		this.elements.each(function(element) {
			this.buildMenu(element);
		}, this);
	},
	
	buildMenu: function(element) {
		// create closure
		var self = this;
		
		// add container class (for styling)
		element.addClass(self.options.containerClass);
		
		// create item instances
		var boxes = element.getElements(self.options.boxes);
		var labels = element.getElements(self.options.labels);
		
		// list container
		var list = new Element('ul', {
			'styles': { display: 'none' },
			'events': {
				'mouseenter': function() { self.action = 'open'; }, 
				'mouseleave': function() { 
					self.action = 'close';
					self.itemHover(this, 'none');
				},
				
				'mousedown': function(e) { e.stop(); }, // stop text selection
				'selectstart': function() { return false; }, // stop IE text selection
				
				'keydown': function(e) {
					if (e.key == 'esc') {
						self.toggleMenu('close', monitor, this);
					}
					else if (e.key == 'down' || e.key == 'up') {
						self.itemHover(this, e.key);
					}
				}
			}
		});
		// list items
		boxes.each(function(box, i) {
			box.addEvents({
				'click': function(e) {
					e.stop();
				},
				'keydown': function(e) {
					if (e.key == 'space') {
						self.active = true;
						self.changeItemState(this.getParent(), this, monitor);
					}
					if (self.active && (e.key == 'down' || e.key == 'up')) {
						self.changeItemState(this.getParent(), this, monitor);
					}
				},
				'keyup': function(e) {
					if (e.key == 'space') {
						self.active = false;
					}
				}
			});
			var label = labels[i];
			new Element('li', {
				'class': box.get('checked') ? self.options.itemSelectedClass : '',
				'events': {
					'mouseenter': function() {
						if (self.active === true) {
							self.changeItemState(this, box, monitor);
						}
						self.itemHover(list, this);
					},
					'mousedown': function() {
						self.active = true;
						self.changeItemState(this, box, monitor);
					}
				}
			}).adopt([box, label]).inject(list);
		});
		// list monitor
		var monitor = new Element('div', {
			'class': self.options.monitorClass,
			'html': '<div><div>' + self.changeMonitorValue(list) + '</div></div>',
			'tabindex': 0,
			'events': {
				'mouseenter': function() { self.action = 'open'; }, 
				'mouseleave': function() { self.action = 'close'; },
				'click': function() { 
					
					if (this.hasClass(self.options.monitorActiveClass)) {
						self.toggleMenu('close', monitor, list);
					}
					else {
						self.toggleMenu('open', monitor, list);
					}
				},
				'keydown': function(e) {
					if (e.key == 'space' || e.key == 'down' || e.key == 'up') {
						self.action = 'close';
						self.toggleMenu('open', monitor, list);
					}
				},
				
				'mousedown': function(e) { e.stop(); }, // stop text selection
				'selectstart': function() { return false; } // stop IE text selection
			}
		});
		// 'global' events
		document.addEvents({
			'mouseup': function() { self.active = false; },
			'click': function() {
				if (self.action == 'close') {
					self.toggleMenu('close', monitor, list);
				}
			},
			'keydown': function(e) {
				if (e.key == 'esc') {
					self.toggleMenu('close', monitor, list);
					self.itemHover(list, 'none');
				}
				if (self.state == 'opened' && (e.key == 'down' || e.key == 'up')) {
					e.stop();
				}
			}
		});
		// replace element content
		element.empty().adopt([monitor, list]);
	}, 
	
	append: function(selector) {
		var elements = document.getElements(selector);
		this.elements.combine(elements);
		
		elements.each(function(element) {
			this.buildMenu(element);
		}, this);
	}, 
	
	changeItemState: function(item, checkbox, monitor) {
		if (item.hasClass(this.options.itemSelectedClass)) {
			item.removeClass(this.options.itemSelectedClass);
			checkbox.set('checked', false).focus();
		}
		else {
			item.addClass(this.options.itemSelectedClass);
			checkbox.set('checked', true).focus();
		}
		
		monitor.set('html', '<div><div>' + this.changeMonitorValue(item.getParent()) + '</div></div>');
	}, 
	
	changeMonitorValue: function(list) {
		var text = list.getElements(this.options.boxes).filter(function(box) {
			return box.get('checked');
		}).length + this.options.monitorText;
		
		return text;
	}, 
	
	itemHover: function(list, select) {
		var current = list.getElement('li.'+this.options.itemHoverClass);
		
		switch (select) {
			case 'down':
				if (current && (sibling = current.getNext())) current.removeClass(this.options.itemHoverClass);
				else this.itemHover(list, 'last');
				break;
 			case 'up':
				if (current && (sibling = current.getPrevious())) current.removeClass(this.options.itemHoverClass);
				else this.itemHover(list, 'first');
				break;
			case 'none':
				list.getElements('li.'+this.options.itemHoverClass).removeClass(this.options.itemHoverClass);
				break;
			case 'first':
				var sibling = list.getFirst();
				break;
			case 'last':
				var sibling = list.getLast();
				break;
			default:
				if (current) current.removeClass(this.options.itemHoverClass);
				var sibling = select;
				break;
		}
		
		if (sibling) 
			sibling.addClass(this.options.itemHoverClass).getElement(this.options.boxes).focus();
	},
	
	toggleMenu: function(toggle, monitor, list) {
		if (toggle == 'open') {
			monitor.addClass(this.options.monitorActiveClass);
			list.setStyle('display', '');
			this.itemHover(list, 'first');
			
			this.state = 'opened';
		}
		else {
			// close all MultiSelect menus
			this.elements.getElement('div.monitor').removeClass(this.options.monitorActiveClass);
			this.elements.getElement('ul').setStyle('display', 'none');

			this.action = 'open';
			this.state = 'closed';
		}
		
		if (list.getScrollSize().y > (list.getStyle('max-height').toInt() ? list.getStyle('max-height').toInt() : list.getStyle('height').toInt()))
			list.setStyle('overflow-y', 'scroll');
	}
});