/**
 * Class representing charge rows.
 *
 * This description should be revised and expanded.
 *
 * @module Assets
 */
var Q = require('Q');
var Db = Q.require('Db');
var Charge = Q.require('Base/Assets/Charge');

/**
 * Class representing 'Charge' rows in the 'Assets' database
 * @namespace Assets
 * @class Charge
 * @extends Base.Assets.Charge
 * @constructor
 * @param {Object} fields The fields values to initialize table row as
 * an associative array of `{column: value}` pairs
 */
function Assets_Charge (fields) {

	// Run mixed-in constructors
	Assets_Charge.constructors.apply(this, arguments);

	/*
	 * Add any privileged methods to the model class here.
	 * Public methods should probably be added further below.
	 * If file 'Charge.js.inc' exists, its content is included
	 * * * */

	/* * * */
}

Q.mixin(Assets_Charge, Charge);

/*
 * Add any public methods here by assigning them to Assets_Charge.prototype
 */

/**
 * The setUp() method is called the first time
 * an object of this class is constructed.
 * @method setUp
 */
Assets_Charge.prototype.setUp = function () {
	// put any code here
	// overrides the Base class
};

module.exports = Assets_Charge;