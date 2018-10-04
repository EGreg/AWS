/**
 * Autogenerated base class for the Websites model.
 * 
 * Don't change this file, since it can be overwritten.
 * Instead, change the Websites.js file.
 *
 * @module Websites
 */
var Q = require('Q');
var Db = Q.require('Db');

/**
 * Base class for the Websites model
 * @namespace Base
 * @class Websites
 * @static
 */
function Base () {
	return this;
}
 
module.exports = Base;

/**
 * The list of model classes
 * @property tableClasses
 * @type array
 */
Base.tableClasses = [
	"Websites_Article",
	"Websites_Permalink"
];

/**
 * This method calls Db.connect() using information stored in the configuration.
 * If this has already been called, then the same db object is returned.
 * @method db
 * @return {Db} The database connection
 */
Base.db = function () {
	return Db.connect('Websites');
};

/**
 * The connection name for the class
 * @method connectionName
 * @return {string} The name of the connection
 */
Base.connectionName = function() {
	return 'Websites';
};

/**
 * Link to Websites.Article model
 * @property Article
 * @type Websites.Article
 */
Base.Article = Q.require('Websites/Article');

/**
 * Link to Websites.Permalink model
 * @property Permalink
 * @type Websites.Permalink
 */
Base.Permalink = Q.require('Websites/Permalink');