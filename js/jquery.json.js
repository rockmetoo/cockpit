jQuery.extend({
	rpcJSON: function(url, method, params, passthrough, callback) {
		var rpc = {"method": method, "params": jsonify(params), "passthrough": jsonify(passthrough)};
		$.get(url, rpc, function(data) {
			callback(eval("("+data+")"));
		});
	}
});


function jsonify(value) {
	jsonify(value, null);
}

function jsonify(value, whitelist) {
	var a,		  // The array holding the partial texts.
		i,		  // The loop counter.
		k,		  // The member key.
		l,		  // Length.
		r = /["\\\x00-\x1f\x7f-\x9f]/g,
		v;		  // The member value.
	switch (typeof value) {
		case 'string':
			// If the string contains no control characters, no quote characters, and no
			// backslash characters, then we can safely slap some quotes around it.
			// Otherwise we must also replace the offending characters with safe sequences.
			return r.test(value) ?
				'"' + value.replace(r, function (a) {
					var c = m[a];
					if (c) {
						return c;
					}
					c = a.charCodeAt();
					return '\\u00' + Math.floor(c / 16).toString(16) +
											   (c % 16).toString(16);
				}) + '"' :
				'"' + value + '"';
		case 'number':
			// JSON numbers must be finite. Encode non-finite numbers as null.
			return isFinite(value) ? String(value) : 'null';
		case 'boolean':
		case 'null':
			return String(value);
		case 'object':
			// Due to a specification blunder in ECMAScript,
			// typeof null is 'object', so watch out for that case.
			if (!value) {
				return 'null';
			}
			// If the object has a toJSON method, call it, and jsonify the result.
			if (typeof value.toJSON === 'function') {
				return jsonify(value.toJSON());
			}
			a = [];
			if (typeof value.length === 'number' &&
					!(value.propertyIsEnumerable('length'))) {
				// The object is an array. jsonify every element. Use null as a placeholder
				// for non-JSON values.
				l = value.length;
				for (i = 0; i < l; i += 1) {
					a.push(jsonify(value[i], whitelist) || 'null');
				}
				// Join all of the elements together and wrap them in brackets.
				return '[' + a.join(',') + ']';
			}
			if (whitelist) {
				// If a whitelist (array of keys) is provided, use it to select the components
				// of the object.
				l = whitelist.length;
				for (i = 0; i < l; i += 1) {
					k = whitelist[i];
					if (typeof k === 'string') {
						v = jsonify(value[k], whitelist);
						if (v) {
							a.push(jsonify(k) + ':' + v);
						}
					}
				}
			} else {
				// Otherwise, iterate through all of the keys in the object.
				for (k in value) {
					if (typeof k === 'string') {
						v = jsonify(value[k], whitelist);
						if (v) {
							a.push(jsonify(k) + ':' + v);
						}
					}
				}
			}
			// Join all of the member texts together and wrap them in braces.
			return '{' + a.join(',') + '}';
	}
}

function clone(obj) {
	if(typeof(obj) != 'object') return obj;
	else if(obj == null) return null;
	
	var newObj = Object();
	
	for(var k in obj) {
		newObj[k] = clone(obj[k]);
	}
	return newObj;
}
	