<?php
/** ---------------------------------------------------------------------
 * app/models/ca_objects.php : table access class for table ca_objects
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2008-2014 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 * 
 * @package CollectiveAccess
 * @subpackage models
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 * 
 * ----------------------------------------------------------------------
 */
 
 /**
   *
   */

require_once(__CA_LIB_DIR__."/ca/IBundleProvider.php");
require_once(__CA_LIB_DIR__."/ca/RepresentableBaseModel.php");
require_once(__CA_MODELS_DIR__."/ca_object_representations.php");
require_once(__CA_MODELS_DIR__."/ca_objects_x_object_representations.php");
require_once(__CA_MODELS_DIR__."/ca_loans_x_objects.php");
require_once(__CA_MODELS_DIR__."/ca_objects_x_storage_locations.php");
require_once(__CA_MODELS_DIR__."/ca_commerce_orders.php");
require_once(__CA_MODELS_DIR__."/ca_commerce_order_items.php");
require_once(__CA_MODELS_DIR__."/ca_object_lots.php");
require_once(__CA_APP_DIR__."/helpers/mediaPluginHelpers.php");


BaseModel::$s_ca_models_definitions['ca_objects'] = array(
 	'NAME_SINGULAR' 	=> _t('object'),
 	'NAME_PLURAL' 		=> _t('objects'),
 	'FIELDS' 			=> array(
		'object_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN, 
				'IDENTITY' => true, 'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('CollectiveAccess id'), 'DESCRIPTION' => _t('Unique numeric identifier used by CollectiveAccess internally to identify this object')
		),
		'parent_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_OMIT, 
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => '',
				'LABEL' => 'Parent id', 'DESCRIPTION' => 'Identifier of parent object; is null if object is root of hierarchy.'
		),
		'hier_object_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_OMIT, 
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => 'Object hierarchy', 'DESCRIPTION' => 'Identifier of object that is root of the object hierarchy.'
		),
		'lot_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN, 
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'ALLOW_BUNDLE_ACCESS_CHECK' => true, 'DONT_ALLOW_IN_UI' => true,
				'DEFAULT' => '',
				'LABEL' => _t('Lot'), 'DESCRIPTION' => _t('Lot this object belongs to; is null if object is not part of a lot.')
		),
		'locale_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DISPLAY_FIELD' => array('ca_locales.name'),
				'DEFAULT' => '',
				'LABEL' => _t('Locale'), 'DESCRIPTION' => _t('The locale from which the object originates.')
		),
		'source_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => '',
				'ALLOW_BUNDLE_ACCESS_CHECK' => true,
				'LIST_CODE' => 'object_sources',
				'LABEL' => _t('Source'), 'DESCRIPTION' => _t('Administrative source of object. This value is often used to indicate the administrative sub-division or legacy database from which the object originates, but can also be re-tasked for use as a simple classification tool if needed.')
		),
		'type_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LIST_CODE' => 'object_types',
				'LABEL' => _t('Type'), 'DESCRIPTION' => _t('The type of the object. In CollectiveAccess every object has a single "instrinsic" type that determines the set of descriptive, technical and administrative metadata that can be applied to it. As such this type is "low-level" and directly tied to the form of the object - eg. photograph, book, analog video recording, etc.')
		),
		'idno' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'ALLOW_BUNDLE_ACCESS_CHECK' => true,
				'LABEL' => _t('Object identifier'), 'DESCRIPTION' => _t('A unique alphanumeric identifier for this object. This is usually equivalent to the "accession number" in museum settings.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'idno_sort' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_OMIT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => 'Sortable object identifier', 'DESCRIPTION' => 'Value used for sorting objects on identifier value.',
				'BOUNDS_LENGTH' => array(0,255)
		),
		'item_status_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => '',
				'LIST_CODE' => 'object_statuses',
				'LABEL' => _t('Accession status'), 'DESCRIPTION' => _t('Indicates accession/collection status of object. (eg. accessioned, pending accession, loan, non-accessioned item, etc.)')
		),
		'acquisition_type_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => '',
				'ALLOW_BUNDLE_ACCESS_CHECK' => true,
				'LIST_CODE' => 'object_acq_types',
				'LABEL' => _t('Acquisition method'), 'DESCRIPTION' => _t('Indicates method employed to acquire the object.')
		),
		'source_info' => array(
				'FIELD_TYPE' => FT_VARS, 'DISPLAY_TYPE' => DT_OMIT, 
				'DISPLAY_WIDTH' => 88, 'DISPLAY_HEIGHT' => 15,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => 'Source information', 'DESCRIPTION' => 'Serialized array used to store source information for object information retrieved via web services [NOT IMPLEMENTED YET].'
		),
		'hier_left' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_OMIT, 
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => 'Hierarchical index - left bound', 'DESCRIPTION' => 'Left-side boundary for nested set-style hierarchical indexing; used to accelerate search and retrieval of hierarchical record sets.'
		),
		'hier_right' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_OMIT, 
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => 'Hierarchical index - right bound', 'DESCRIPTION' => 'Right-side boundary for nested set-style hierarchical indexing; used to accelerate search and retrieval of hierarchical record sets.'
		),
		'extent' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'ALLOW_BUNDLE_ACCESS_CHECK' => true,
				'LABEL' => _t('Extent'), 'DESCRIPTION' => _t('The extent of the object. This is typically the number of discrete items that compose the object represented by this record. It is stored as a whole number (eg. 1, 2, 3...).')
		),
		'extent_units' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'ALLOW_BUNDLE_ACCESS_CHECK' => true,
				'LABEL' => _t('Extent units'), 'DESCRIPTION' => _t('Units of extent value. (eg. pieces, items, components, reels, etc.)'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'access' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'ALLOW_BUNDLE_ACCESS_CHECK' => true,
				'BOUNDS_CHOICE_LIST' => array(
					_t('Not accessible to public') => 0,
					_t('Accessible to public') => 1
				),
				'LIST' => 'access_statuses',
				'LABEL' => _t('Access'), 'DESCRIPTION' => _t('Indicates if object is accessible to the public or not.')
		),
		'status' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'ALLOW_BUNDLE_ACCESS_CHECK' => true,
				'BOUNDS_CHOICE_LIST' => array(
					_t('Newly created') => 0,
					_t('Editing in progress') => 1,
					_t('Editing complete - pending review') => 2,
					_t('Review in progress') => 3,
					_t('Completed') => 4
				),
				'LIST' => 'workflow_statuses',
				'LABEL' => _t('Status'), 'DESCRIPTION' => _t('Indicates the current state of the object record.')
		),
		'deleted' => array(
				'FIELD_TYPE' => FT_BIT, 'DISPLAY_TYPE' => DT_OMIT, 
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Is deleted?'), 'DESCRIPTION' => _t('Indicates if the object is deleted or not.'),
				'BOUNDS_VALUE' => array(0,1)
		),
		'rank' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Sort order'), 'DESCRIPTION' => _t('Sort order'),
		),
		'acl_inherit_from_ca_collections' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 100, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'ALLOW_BUNDLE_ACCESS_CHECK' => true,
				'BOUNDS_CHOICE_LIST' => array(
					_t('Do not inherit access settings from related collections') => 0,
					_t('Inherit access settings from related collections') => 1
				),
				'LABEL' => _t('Inherit access settings from collections?'), 'DESCRIPTION' => _t('Determines whether access settings set for related collections are applied to this object.')
		),
		'acl_inherit_from_parent' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 100, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'ALLOW_BUNDLE_ACCESS_CHECK' => true,
				'BOUNDS_CHOICE_LIST' => array(
					_t('Do not inherit access settings from parent') => 0,
					_t('Inherit access settings from parent') => 1
				),
				'LABEL' => _t('Inherit access settings from parent?'), 'DESCRIPTION' => _t('Determines whether access settings set for parent objects are applied to this object.')
		)
	)
);

class ca_objects extends RepresentableBaseModel implements IBundleProvider {
	# ------------------------------------------------------
	# --- Object attribute properties
	# ------------------------------------------------------
	# Describe structure of content object's properties - eg. database fields and their
	# associated types, what modes are supported, et al.
	#

	# ------------------------------------------------------
	# --- Basic object parameters
	# ------------------------------------------------------
	# what table does this class represent?
	protected $TABLE = 'ca_objects';
	      
	# what is the primary key of the table?
	protected $PRIMARY_KEY = 'object_id';

	# ------------------------------------------------------
	# --- Properties used by standard editing scripts
	# 
	# These class properties allow generic scripts to properly display
	# records from the table represented by this class
	#
	# ------------------------------------------------------

	# Array of fields to display in a listing of records from this table
	protected $LIST_FIELDS = array('idno');

	# When the list of "list fields" above contains more than one field,
	# the LIST_DELIMITER text is displayed between fields as a delimiter.
	# This is typically a comma or space, but can be any string you like
	protected $LIST_DELIMITER = ' ';

	# What you'd call a single record from this table (eg. a "person")
	protected $NAME_SINGULAR;

	# What you'd call more than one record from this table (eg. "people")
	protected $NAME_PLURAL;

	# List of fields to sort listing of records by; you can use 
	# SQL 'ASC' and 'DESC' here if you like.
	protected $ORDER_BY = array('idno');

	# Maximum number of record to display per page in a listing
	protected $MAX_RECORDS_PER_PAGE = 20; 

	# How do you want to page through records in a listing: by number pages ordered
	# according to your setting above? Or alphabetically by the letters of the first
	# LIST_FIELD?
	protected $PAGE_SCHEME = 'alpha'; # alpha [alphabetical] or num [numbered pages; default]

	# If you want to order records arbitrarily, add a numeric field to the table and place
	# its name here. The generic list scripts can then use it to order table records.
	protected $RANK = 'rank';
	
	# ------------------------------------------------------
	# Hierarchical table properties
	# ------------------------------------------------------
	protected $HIERARCHY_TYPE				=	__CA_HIER_TYPE_ADHOC_MONO__;
	protected $HIERARCHY_LEFT_INDEX_FLD 	= 	'hier_left';
	protected $HIERARCHY_RIGHT_INDEX_FLD 	= 	'hier_right';
	protected $HIERARCHY_PARENT_ID_FLD		=	'parent_id';
	protected $HIERARCHY_DEFINITION_TABLE	=	'ca_objects';
	protected $HIERARCHY_ID_FLD				=	'hier_object_id';
	protected $HIERARCHY_POLY_TABLE			=	null;
	
	# ------------------------------------------------------
	# Change logging
	# ------------------------------------------------------
	protected $UNIT_ID_FIELD = null;
	protected $LOG_CHANGES_TO_SELF = true;
	protected $LOG_CHANGES_USING_AS_SUBJECT = array(
		"FOREIGN_KEYS" => array(
			'lot_id'
		),
		"RELATED_TABLES" => array(
		
		)
	);

	# ------------------------------------------------------
	# Labeling
	# ------------------------------------------------------
	protected $LABEL_TABLE_NAME = 'ca_object_labels';
	
	# ------------------------------------------------------
	# Attributes
	# ------------------------------------------------------
	protected $ATTRIBUTE_TYPE_ID_FLD = 'type_id';			// name of type field for this table - attributes system uses this to determine via ca_metadata_type_restrictions which attributes are applicable to rows of the given type
	protected $ATTRIBUTE_TYPE_LIST_CODE = 'object_types';	// list code (ca_lists.list_code) of list defining types for this table

	# ------------------------------------------------------
	# Sources
	# ------------------------------------------------------
	protected $SOURCE_ID_FLD = 'source_id';				// name of source field for this table
	protected $SOURCE_LIST_CODE = 'object_sources';		// list code (ca_lists.list_code) of list defining sources for this table
	
	# ------------------------------------------------------
	# Self-relations
	# ------------------------------------------------------
	protected $SELF_RELATION_TABLE_NAME = 'ca_objects_x_objects';
	
	# ------------------------------------------------------
	# ID numbering
	# ------------------------------------------------------
	protected $ID_NUMBERING_ID_FIELD = 'idno';				// name of field containing user-defined identifier
	protected $ID_NUMBERING_SORT_FIELD = 'idno_sort';		// name of field containing version of identifier for sorting (is normalized with padding to sort numbers properly)
	
	# ------------------------------------------------------
	# Search
	# ------------------------------------------------------
	protected $SEARCH_CLASSNAME = 'ObjectSearch';
	protected $SEARCH_RESULT_CLASSNAME = 'ObjectSearchResult';
	
	# ------------------------------------------------------
	# ACL
	# ------------------------------------------------------
	protected $SUPPORTS_ACL = true;
	
	# ------------------------------------------------------
	# $FIELDS contains information about each field in the table. The order in which the fields
	# are listed here is the order in which they will be returned using getFields()

	protected $FIELDS;
	
	# ------------------------------------------------------
	# --- Constructor
	#
	# This is a function called when a new instance of this object is created. This
	# standard constructor supports three calling modes:
	#
	# 1. If called without parameters, simply creates a new, empty objects object
	# 2. If called with a single, valid primary key value, creates a new objects object and loads
	#    the record identified by the primary key value
	#
	# ------------------------------------------------------
	public function __construct($pn_id=null) {
		if (
			!is_null(BaseModel::$s_ca_models_definitions['ca_objects']['FIELDS']['acl_inherit_from_parent']['DEFAULT'])
			||
			!is_null(BaseModel::$s_ca_models_definitions['ca_objects']['FIELDS']['acl_inherit_from_ca_collections']['DEFAULT'])
		) {
			$o_config = Configuration::load();
		
			if (!is_null(BaseModel::$s_ca_models_definitions['ca_objects']['FIELDS']['acl_inherit_from_parent']['DEFAULT'])) {
				BaseModel::$s_ca_models_definitions['ca_objects']['FIELDS']['acl_inherit_from_parent']['DEFAULT'] = (int)$o_config->get('ca_objects_acl_inherit_from_parent_default');
			}
			if (!is_null(BaseModel::$s_ca_models_definitions['ca_objects']['FIELDS']['acl_inherit_from_ca_collections']['DEFAULT'])) {
				BaseModel::$s_ca_models_definitions['ca_objects']['FIELDS']['acl_inherit_from_ca_collections']['DEFAULT'] = (int)$o_config->get('ca_objects_acl_inherit_from_ca_collections_default');
			}
		}
		parent::__construct($pn_id);
	}
	# ------------------------------------------------------
	protected function initLabelDefinitions($pa_options=null) {
		parent::initLabelDefinitions($pa_options);
		$this->BUNDLES['ca_object_representations'] = array('type' => 'related_table', 'repeating' => true, 'label' => _t('Media representations'));
		$this->BUNDLES['ca_objects'] = array('type' => 'related_table', 'repeating' => true, 'label' => _t('Related objects'));
		$this->BUNDLES['ca_entities'] = array('type' => 'related_table', 'repeating' => true, 'label' => _t('Related entities'));
		$this->BUNDLES['ca_places'] = array('type' => 'related_table', 'repeating' => true, 'label' => _t('Related places'));
		$this->BUNDLES['ca_occurrences'] = array('type' => 'related_table', 'repeating' => true, 'label' => _t('Related occurrences'));
		$this->BUNDLES['ca_collections'] = array('type' => 'related_table', 'repeating' => true, 'label' => _t('Related collections'));
		$this->BUNDLES['ca_storage_locations'] = array('type' => 'related_table', 'repeating' => true, 'label' => _t('Related storage locations'));
		$this->BUNDLES['ca_loans'] = array('type' => 'related_table', 'repeating' => true, 'label' => _t('Related loans'));
		$this->BUNDLES['ca_movements'] = array('type' => 'related_table', 'repeating' => true, 'label' => _t('Related movements'));
		
		$this->BUNDLES['ca_tour_stops'] = array('type' => 'related_table', 'repeating' => true, 'label' => _t('Related tour stops'));
		
		$this->BUNDLES['ca_object_lots'] = array('type' => 'related_table', 'repeating' => true, 'label' => _t('Related lot'));
		
		$this->BUNDLES['ca_list_items'] = array('type' => 'related_table', 'repeating' => true, 'label' => _t('Related vocabulary terms'));
		$this->BUNDLES['ca_sets'] = array('type' => 'special', 'repeating' => true, 'label' => _t('Sets'));
		
		$this->BUNDLES['hierarchy_navigation'] = array('type' => 'special', 'repeating' => false, 'label' => _t('Hierarchy navigation'));
		$this->BUNDLES['hierarchy_location'] = array('type' => 'special', 'repeating' => false, 'label' => _t('Location in hierarchy'));
		
		$this->BUNDLES['ca_commerce_order_history'] = array('type' => 'special', 'repeating' => false, 'label' => _t('Order history'));
		$this->BUNDLES['ca_objects_components_list'] = array('type' => 'special', 'repeating' => false, 'label' => _t('Components'));
		$this->BUNDLES['ca_objects_location'] = array('type' => 'special', 'repeating' => false, 'label' => _t('Object location'));
		$this->BUNDLES['ca_objects_history'] = array('type' => 'special', 'repeating' => false, 'label' => _t('Object use history'));
	}
	# ------------------------------------------------------
	/**
	 * Override set() to do idno_stub lookups on lots
	 *
	 * @param mixed $pm_fields
	 * @param mixed $pm_value
	 * @param array $pa_options Most options are handled by subclasses. Options defined here include:
	 *		assumeIdnoStubForLotID = set to force lookup of lot_id values as ca_object_lots.idno_stub values first not matter what, before consideration as a numeric lot_id. The default is false, in which case integer values are considered lot_ids and non-numeric values possible idno_stubs.
	 *		
	 * @return int 
	 */
	public function set($pm_fields, $pm_value="", $pa_options=null) {
		if (!is_array($pm_fields)) {
			$pm_fields = array($pm_fields => $pm_value);
		}
		$pb_assume_idno_stub_for_lot_id = caGetOption('assumeIdnoStubForLotID', $pa_options, false);
		foreach($pm_fields as $vs_fld => $vs_val) {
			if (($vs_fld == 'lot_id') && ($pb_assume_idno_stub_for_lot_id || preg_match("![^\d]+!", $vs_val))) {
				$t_lot = new ca_object_lots();
				if ($t_lot->load(array('idno_stub' => $vs_val))) {
					$vn_lot_id = (int)$t_lot->getPrimaryKey();
					$pm_fields[$vs_fld] = $vn_lot_id;
				}
			}
		}
		return parent::set($pm_fields, null, $pa_options);
	}
	# ------------------------------------------------------
	public function delete($pb_delete_related=false, $pa_options=null, $pa_fields=null, $pa_table_list=null){
		// nuke related representations
		foreach($this->getRepresentations() as $va_rep){
			// check if representation is in use anywhere else 
			$qr_res = $this->getDb()->query("SELECT count(*) c FROM ca_objects_x_object_representations WHERE object_id <> ? AND representation_id = ?", (int)$this->getPrimaryKey(), (int)$va_rep["representation_id"]);
			if ($qr_res->nextRow() && ($qr_res->get('c') == 0)) {
				$this->removeRepresentation($va_rep["representation_id"], array('dontCheckPrimaryValue' => true));
			}
		}

		return parent::delete($pb_delete_related, $pa_options, $pa_fields, $pa_table_list);
	}
	# ------------------------------------------------------
	/**
	 * @param array $pa_options
	 *		duplicate_media
	 */
	public function duplicate($pa_options=null) {
		$vb_we_set_transaction = false;
		if (!$this->inTransaction()) {
			$this->setTransaction($o_t = new Transaction($this->getDb()));
			$vb_we_set_transaction = true;
		} else {
			$o_t = $this->getTransaction();
		}
		
		if ($t_dupe = parent::duplicate($pa_options)) {
			$vb_duplicate_media = isset($pa_options['duplicate_media']) && $pa_options['duplicate_media'];
		
			if ($vb_duplicate_media) { 
				// Try to link representations
				$o_db = $this->getDb();
				
				$qr_res = $o_db->query("
					SELECT *
					FROM ca_objects_x_object_representations
					WHERE object_id = ?
				", (int)$this->getPrimaryKey());
				
				$va_reps = array();
				while($qr_res->nextRow()) {
					$va_reps[$qr_res->get('representation_id')] = $qr_res->getRow();
				}
				
				$t_object_x_rep = new ca_objects_x_object_representations();
				foreach($va_reps as $vn_representation_id => $va_rep) {
					$t_object_x_rep->setMode(ACCESS_WRITE);
					$va_rep['object_id'] = $t_dupe->getPrimaryKey();
					$t_object_x_rep->set($va_rep);
					$t_object_x_rep->insert();
					
					if ($t_object_x_rep->numErrors()) {
						$this->errors = $t_object_x_rep->errors;
						if ($vb_we_set_transaction) { $o_t->rollback();}
						return false;
					}
				}
			}
		} else {
			if ($vb_we_set_transaction) { $o_t->rollback(); }
			return false;
		}
		
		
		if ($vb_we_set_transaction) { $o_t->commit();}
		return $t_dupe;
	}
 	# ------------------------------------------------------
 	# Client services
 	# ------------------------------------------------------
 	/**
 	 *
 	 */
 	public function isOnLoan() {
 		if (!$this->getPrimaryKey()) { return null; }
 		$t_order = new ca_commerce_orders();
 		if (is_array($va_orders = $t_order->getOrders(array('object_id' => $this->getPrimaryKey(), 'type' => 'L'))) && sizeof($va_orders)) {
 			$va_order = array_shift($va_orders);
 			$t_order_item = new ca_commerce_order_items();
 			if ($t_order_item->load(array('order_id' => $va_order['order_id'], 'object_id' => $this->getPrimaryKey()))) {
 				if (!$t_order_item->get('loan_return_date', array('getDirectDate' => true))) {
 					return array(
 						'loan_checkout_date' => $t_order_item->get('loan_checkout_date'),
 						'loan_checkout_date_raw' => $t_order_item->get('loan_checkout_date', array('getDirectDate' => true)),
 						'loan_due_date' => $t_order_item->get('loan_due_date'),
 						'loan_due_date_raw' => $t_order_item->get('loan_due_date', array('getDirectDate' => true)),
 						'client' => $va_order['billing_fname'].' '.$va_order['billing_lname']." (".$va_order['billing_email'].")",
 						'billing_fname' => $va_order['billing_fname'],
 						'billing_lname' => $va_order['billing_lname'],
 						'billing_email' => $va_order['billing_email'],
 						'order_id' => $va_order['order_id']
 					);
 				}
 			}
 		}
 		return false;
 	}
 	# ------------------------------------------------------
 	/**
 	 * Returns history of client orders. Orders are sorted most recent first.
 	 *
 	 * @param string $ps_order_type Type of order to return history for. L=loans, O=sales orders. If set to any other value all types of orders will be returned.
 	 * @return array List of orders
 	 */
 	public function getClientHistory($ps_order_type) {
 		if (!$this->getPrimaryKey()) { return null; }
 		$vn_object_id = $this->getPrimaryKey();
 		$ps_order_type = strtoupper($ps_order_type);
 		
 		$va_options = array();
 		if (!in_array($ps_order_type, array('O', 'L'))) { $ps_order_type = null; } else { $va_options['type'] = $ps_order_type; }
 		
 		$va_orders = ca_commerce_orders::getUsageOfItemInOrders($vn_object_id, $va_options);
 		
 		$va_history = array();
		foreach($va_orders as $vn_id => $va_order) {
			$va_order['loan_checkout_date_raw'] = $va_order['loan_checkout_date'];
			$va_order['loan_checkout_date'] = caGetLocalizedDate($va_order['loan_checkout_date'], array('timeOmit' => true, 'dateFormat' => 'delimited')); 
			
			$va_order['loan_due_date_raw'] = $va_order['loan_due_date'];
			$va_order['loan_due_date'] = $va_order['loan_due_date'] ? caGetLocalizedDate($va_order['loan_due_date'], array('timeOmit' => true, 'dateFormat' => 'delimited')) : ''; 
			
			$va_order['loan_return_date_raw'] = $va_order['loan_return_date'];
			$va_order['loan_return_date'] = $va_order['loan_return_date'] ? caGetLocalizedDate($va_order['loan_return_date'], array('timeOmit' => true, 'dateFormat' => 'delimited')) : ''; 
			
			$va_order['order_number'] = ca_commerce_orders::generateOrderNumber($va_order['order_id'], $va_order['created_on']);
			$va_history[$va_order['loan_checkout_date']] = $va_order;
		}
		ksort($va_history);
		return array_reverse($va_history);;
 	}
 	# ------------------------------------------------------
 	/**
 	 * Return history for client loans of the currently loaded object
 	 *
 	 * @return array Loan history
 	 */
 	public function getClientLoanHistory() {
 		return $this->getClientHistory('L');
 	}
 	# ------------------------------------------------------
 	/**
 	 * Return history for client sales orders that include the currently loaded object
 	 *
 	 * @return array Loan history
 	 */
 	public function getClientOrderHistory() {
 		return $this->getClientHistory('O');
 	}
 	# ------------------------------------------------------
 	# HTML form bundles
 	# ------------------------------------------------------
	/** 
	 * Returns HTML form bundle (for use in a ca_object_representations editor form) for media
	 *
	 * @param HTTPRequest $po_request The current request
	 * @param string $ps_form_name
	 * @param string $ps_placement_code
	 * @param array $pa_bundle_settings
	 * @param array $pa_options Array of options. Supported options are 
	 *			noCache = If set to true then label cache is bypassed; default is true
	 *
	 * @return string Rendered HTML bundle
	 */
	public function getCommerceOrderHistoryHTMLFormBundle($po_request, $ps_form_name, $ps_placement_code, $pa_bundle_settings=null, $pa_options=null) {
		global $g_ui_locale;
		
		$o_view = new View($po_request, $po_request->getViewsDirectoryPath().'/bundles/');
		
		if(!is_array($pa_options)) { $pa_options = array(); }
		
		$o_view->setVar('id_prefix', $ps_form_name.'_commerce_order_history');
		$o_view->setVar('placement_code', $ps_placement_code);		// pass placement code
		
		$o_view->setVar('settings', $pa_bundle_settings);
		
		$o_view->setVar('t_subject', $this);
		
		
		
		return $o_view->render('ca_commerce_order_history.php');
	}
	# ------------------------------------------------------
 	# Object location tracking
 	# ------------------------------------------------------
 	/**
 	 * Returns HTML form bundle for location tracking
	 *
	 * @param HTTPRequest $po_request The current request
	 * @param string $ps_form_name
	 * @param string $ps_placement_code
	 * @param array $pa_bundle_settings
	 * @param array $pa_options Array of options. Options include:
	 *			None yet.
	 *
	 * @return string Rendered HTML bundle
 	 */
 	public function getObjectLocationHTMLFormBundle($po_request, $ps_form_name, $ps_placement_code, $pa_bundle_settings=null, $pa_options=null) {
 		global $g_ui_locale;
		
		$o_view = new View($po_request, $po_request->getViewsDirectoryPath().'/bundles/');
		
		if(!is_array($pa_options)) { $pa_options = array(); }
		
		$vs_display_template		= caGetOption('displayTemplate', $pa_bundle_settings, _t('No template defined'));
		$vs_history_template		= caGetOption('historyTemplate', $pa_bundle_settings, $vs_display_template);
		
		$o_view->setVar('id_prefix', $ps_form_name);
		$o_view->setVar('placement_code', $ps_placement_code);		// pass placement code
		
		$o_view->setVar('settings', $pa_bundle_settings);
		
		$o_view->setVar('add_label', isset($pa_bundle_settings['add_label'][$g_ui_locale]) ? $pa_bundle_settings['add_label'][$g_ui_locale] : null);
		$o_view->setVar('t_subject', $this);
		
		$o_view->setVar('mode', $vs_mode = caGetOption('locationTrackingMode', $pa_bundle_settings, 'ca_movements'));
		
		switch($vs_mode) {
			case 'ca_storage_locations':
				$t_last_location = $this->getLastLocation(array());
				$o_view->setVar('current_location', $t_last_location ? $t_last_location->getWithTemplate($vs_display_template) : null);
				$o_view->setVar('location_history', $this->getLocationHistory(array('template' => $vs_history_template)));
				$o_view->setVar('location_relationship_type', is_array($pa_bundle_settings['ca_storage_locations_relationshipType']) ? addslashes($pa_bundle_settings['ca_storage_locations_relationshipType'][0]) : '');
				$o_view->setVar('location_change_url',  null);
				break;
			case 'ca_movements':
			default:
				$t_last_movement = $this->getLastMovement(array('dateElement' => caGetOption('ca_movements_dateElement', $pa_bundle_settings, null)));
				$o_view->setVar('current_location', $t_last_movement ? $t_last_movement->getWithTemplate($vs_display_template) : null);
				$o_view->setVar('location_history', $this->getMovementHistory(array('dateElement' => caGetOption('ca_movements_dateElement', $pa_bundle_settings, null), 'template' => $vs_history_template)));
				
				$o_view->setVar('location_relationship_type', is_array($pa_bundle_settings['ca_movements_relationshipType']) ? addslashes($pa_bundle_settings['ca_movements_relationshipType'][0]) : '');
				$o_view->setVar('location_change_url', caNavUrl($po_request, 'editor/movements', 'MovementQuickAdd', 'Form', array('movement_id' => 0)));
				break;
		}
		
		
		return $o_view->render('ca_objects_location.php');
 	}
 	# ------------------------------------------------------
 	/**
 	 * Returns HTML form bundle for location tracking
	 *
	 * @param HTTPRequest $po_request The current request
	 * @param string $ps_form_name
	 * @param string $ps_placement_code
	 * @param array $pa_bundle_settings
	 * @param array $pa_options Array of options. Options include:
	 *			None yet.
	 *
	 * @return string Rendered HTML bundle
 	 */
 	public function getObjectHistoryHTMLFormBundle($po_request, $ps_form_name, $ps_placement_code, $pa_bundle_settings=null, $pa_options=null) {
 		global $g_ui_locale;
		
		$o_view = new View($po_request, $po_request->getViewsDirectoryPath().'/bundles/');
		
		if(!is_array($pa_options)) { $pa_options = array(); }
		
		$vs_display_template		= caGetOption('display_template', $pa_bundle_settings, _t('No template defined'));
		$vs_history_template		= caGetOption('history_template', $pa_bundle_settings, $vs_display_template);
		
		$o_view->setVar('id_prefix', $ps_form_name);
		$o_view->setVar('placement_code', $ps_placement_code);		// pass placement code
		
		$o_view->setVar('settings', $pa_bundle_settings);
		
		$o_view->setVar('add_label', isset($pa_bundle_settings['add_label'][$g_ui_locale]) ? $pa_bundle_settings['add_label'][$g_ui_locale] : null);
		$o_view->setVar('t_subject', $this);
		
		//
		// Load update
		//

		$t_loan_rel = new ca_loans_x_objects();
		$o_view->setVar('loan_relationship_types', $t_loan_rel->getRelationshipTypes(null, null,  array_merge($pa_options, $pa_bundle_settings)));
		$o_view->setVar('loan_relationship_types_by_sub_type', $t_loan_rel->getRelationshipTypesBySubtype($this->tableName(), $this->get('type_id'),  array_merge($pa_options, $pa_bundle_settings)));

		$t_location_rel = new ca_objects_x_storage_locations();
		$o_view->setVar('location_relationship_types', $t_location_rel->getRelationshipTypes(null, null,  array_merge($pa_options, $pa_bundle_settings)));
		$o_view->setVar('location_relationship_types_by_sub_type', $t_location_rel->getRelationshipTypesBySubtype($this->tableName(), $this->get('type_id'),  array_merge($pa_options, $pa_bundle_settings)));

		
		
		//
		// Location update
		//
		$o_view->setVar('mode', 'ca_storage_locations'); //$vs_mode = caGetOption('locationTrackingMode', $pa_bundle_settings, 'ca_movements'));
		
		switch($vs_mode) {
			case 'ca_storage_locations':
				$t_last_location = $this->getLastLocation(array());
				$o_view->setVar('current_location', $t_last_location ? $t_last_location->getWithTemplate($vs_display_template) : null);
				$o_view->setVar('location_relationship_type', is_array($pa_bundle_settings['ca_storage_locations_relationshipType']) ? addslashes($pa_bundle_settings['ca_storage_locations_relationshipType'][0]) : '');
				$o_view->setVar('location_change_url',  null);
				break;
			case 'ca_movements':
			default:
				$t_last_movement = $this->getLastMovement(array('dateElement' => caGetOption('ca_movements_dateElement', $pa_bundle_settings, null)));
				$o_view->setVar('current_location', $t_last_movement ? $t_last_movement->getWithTemplate($vs_display_template) : null);
				
				$o_view->setVar('location_relationship_type', is_array($pa_bundle_settings['ca_movements_relationshipType']) ? addslashes($pa_bundle_settings['ca_movements_relationshipType'][0]) : '');
				$o_view->setVar('location_change_url', caNavUrl($po_request, 'editor/movements', 'MovementQuickAdd', 'Form', array('movement_id' => 0)));
				break;
		}
		
		
		$o_media_coder = new MediaInfoCoder();
				
//
// Get history
//
		$va_history = array();
		
		// Lots
		if(is_array($va_lot_types = caGetOption('ca_object_lots_showTypes', $pa_bundle_settings, null)) && ($vn_lot_id = $this->get('lot_id'))) {
			$t_lot = new ca_object_lots($vn_lot_id);
			$va_lot_type_info = $t_lot->getTypeList(); 
			$vn_type_id = $t_lot->get('type_id');
			
			$vs_color = $va_lot_type_info[$vn_type_id]['color'];
			if (!$vs_color || ($vs_color == '000000')) {
				$vs_color = caGetOption("ca_object_lots_{$va_lot_type_info[$vn_type_id]['idno']}_color", $pa_bundle_settings, 'ffffff');
			}
			
			$va_dates = array();
				
			$va_date_elements = caGetOption("ca_object_lots_{$va_lot_type_info[$vn_type_id]['idno']}_dateElement", $pa_bundle_settings, null);
			if (!is_array($va_date_elements) && $va_date_elements) { $va_date_elements = array($va_date_elements); }
			
			if (is_array($va_date_elements) && sizeof($va_date_elements)) {
				foreach($va_date_elements as $vs_date_element) {
					$va_dates[] = array(
						'sortable' => $t_lot->get($vs_date_element, array('getDirectDate' => true)),
						'display' => $t_lot->get($vs_date_element)
					);
				}
			}
			if (!sizeof($va_dates)) {
				$va_dates[] = array(
					'sortable' => $vn_date = caUnixTimestampToHistoricTimestamps($t_lot->getCreationTimestamp(null, array('timestampOnly' => true))),
					'display' => caGetLocalizedDate($vn_date)
				);
			}
			
			foreach($va_dates as $va_date) {
				if (!$va_date['sortable']) { continue; }
				if (!in_array($vn_type_id, $va_lot_types)) { continue; }
				
				
				$va_history[$va_date['sortable']][] = array(
					'type' => 'ca_object_lots',
					'id' => $vn_lot_id,
					'display' => $t_lot->getWithTemplate(caGetOption("ca_object_lots_{$va_lot_type_info[$vn_type_id]['idno']}_displayTemplate", $pa_bundle_settings, '^ca_object_lots.preferred_labels.name (^ca_object_lots.idno_stub)')),
					'color' => $vs_color,
					'icon_url' => $vs_icon_url = $o_media_coder->getMediaTag($va_lot_type_info[$vn_type_id]['icon'], 'icon'),
					'typename_singular' => $vs_typename = $va_lot_type_info[$vn_type_id]['name_singular'],
					'typename_plural' => $va_lot_type_info[$vn_type_id]['name_plural'],
					'icon' => '<div class="caUseHistoryIconContainer" style="background-color: #'.$vs_color.'"><div class="caUseHistoryIcon">'.($vs_icon_url ? $vs_icon_url : '<div class="caUseHistoryIconText">'.$vs_typename.'</div>').'</div></div>',
					'date' => $va_date['display']
				);
			}
		}
		
		// Loans
		$va_loans = $this->get('ca_loans.loan_id', array('returnAsArray' => true));
		if(is_array($va_loan_types = caGetOption('ca_loans_showTypes', $pa_bundle_settings, null)) && is_array($va_loans) && sizeof($va_loans)) {	
			$qr_loans = caMakeSearchResult('ca_loans', $va_loans);
			
			$t_loan = new ca_loans();
			$va_loan_type_info = $t_loan->getTypeList(); 
			
			$va_date_elements_by_type = array();
			foreach($va_loan_types as $vn_type_id) {
				if (!is_array($va_date_elements = caGetOption("ca_loans_{$va_loan_type_info[$vn_type_id]['idno']}_dateElement", $pa_bundle_settings, null)) && $va_date_elements) {
					$va_date_elements = array($va_date_elements);
				}
				if (!$va_date_elements) { continue; }
				$va_date_elements_by_type[$vn_type_id] = $va_date_elements;
			}
			
			while($qr_loans->nextHit()) {
				$vn_loan_id = $qr_loans->get('loan_id');
				$vn_type_id = $qr_loans->get('type_id');
				
				$va_dates = array();
				if (is_array($va_date_elements_by_type[$vn_type_id]) && sizeof($va_date_elements_by_type[$vn_type_id])) {
					foreach($va_date_elements_by_type[$vn_type_id] as $vs_date_element) {
						$va_dates[] = array(
							'sortable' => $qr_loans->get("ca_loans.{$vs_date_element}", array('getDirectDate' => true)),
							'display' => $qr_loans->get("ca_loans.{$vs_date_element}")
						);
					}
				}
				if (!sizeof($va_dates)) {
					$va_dates[] = array(
						'sortable' => $vn_date = caUnixTimestampToHistoricTimestamps($qr_loans->get('lastModified')),
						'display' => caGetLocalizedDate($vn_date)
					);
				}
		
				foreach($va_dates as $va_date) {
					if (!$va_date['sortable']) { continue; }
					if (!in_array($vn_type_id, $va_loan_types)) { continue; }
					
					$vs_color = $va_loan_type_info[$vn_type_id]['color'];
					if (!$vs_color || ($vs_color == '000000')) {
						$vs_color = caGetOption("ca_loans_{$va_loan_type_info[$vn_type_id]['idno']}_color", $pa_bundle_settings, 'ffffff');
					}
					
					$va_history[$va_date['sortable']][] = array(
						'type' => 'ca_loans',
						'id' => $vn_loan_id,
						'display' => $qr_loans->getWithTemplate(caGetOption("ca_loans_{$va_loan_type_info[$vn_type_id]['idno']}_displayTemplate", $pa_bundle_settings, '^ca_loans.preferred_labels.name (^ca_loans.idno)')),
						'color' => $vs_color,
						'icon_url' => $vs_icon_url = $o_media_coder->getMediaTag($va_loan_type_info[$vn_type_id]['icon'], 'icon'),
						'typename_singular' => $vs_typename = $va_loan_type_info[$vn_type_id]['name_singular'],
						'typename_plural' => $va_loan_type_info[$vn_type_id]['name_plural'],
						'icon' => '<div class="caUseHistoryIconContainer" style="background-color: #'.$vs_color.'"><div class="caUseHistoryIcon">'.($vs_icon_url ? $vs_icon_url : '<div class="caUseHistoryIconText">'.$vs_typename.'</div>').'</div></div>',
						'date' => $va_date['display']
					);
				}
			}
		}
		
		// Movements
		$va_movements = $this->get('ca_movements.movement_id', array('returnAsArray' => true));
		if(is_array($va_movement_types = caGetOption('ca_movements_showTypes', $pa_bundle_settings, null)) && is_array($va_movements) && sizeof($va_movements)) {	
			$qr_movements = caMakeSearchResult('ca_movements', $va_movements);
			
			$t_movement = new ca_movements();
			$va_movement_type_info = $t_movement->getTypeList(); 
			
			$va_date_elements_by_type = array();
			foreach($va_movement_types as $vn_type_id) {
				if (!is_array($va_date_elements = caGetOption("ca_movements_{$va_movement_type_info[$vn_type_id]['idno']}_dateElement", $pa_bundle_settings, null)) && $va_date_elements) {
					$va_date_elements = array($va_date_elements);
				}
				if (!$va_date_elements) { continue; }
				$va_date_elements_by_type[$vn_type_id] = $va_date_elements;
			}
			
			while($qr_movements->nextHit()) {
				$vn_movement_id = $qr_movements->get('movement_id');
				$vn_type_id = $qr_movements->get('type_id');
				
				$va_dates = array();
				if (is_array($va_date_elements_by_type[$vn_type_id]) && sizeof($va_date_elements_by_type[$vn_type_id])) {
					foreach($va_date_elements_by_type[$vn_type_id] as $vs_date_element) {
						$va_dates[] = array(
							'sortable' => $qr_movements->get("ca_movements.{$vs_date_element}", array('getDirectDate' => true)),
							'display' => $qr_movements->get("ca_movements.{$vs_date_element}")
						);
					}
				}
				if (!sizeof($va_dates)) {
					$va_dates[] = array(
						'sortable' => $vn_date = caUnixTimestampToHistoricTimestamps($qr_movements->get('lastModified')),
						'display' => caGetLocalizedDate($vn_date)
					);
				}
		
				foreach($va_dates as $va_date) {
					if (!$va_date['sortable']) { continue; }
					if (!in_array($vn_type_id, $va_movement_types)) { continue; }
					
					$vs_color = $va_movement_type_info[$vn_type_id]['color'];
					if (!$vs_color || ($vs_color == '000000')) {
						$vs_color = caGetOption("ca_movements_{$va_movement_type_info[$vn_type_id]['idno']}_color", $pa_bundle_settings, 'ffffff');
					}
					
					$va_history[$va_date['sortable']][] = array(
						'type' => 'ca_movements',
						'id' => $vn_movement_id,
						'display' => $qr_movements->getWithTemplate(caGetOption("ca_movements_{$va_movement_type_info[$vn_type_id]['idno']}_displayTemplate", $pa_bundle_settings, '^ca_movements.preferred_labels.name (^ca_movements.idno)')),
						'color' => $vs_color,
						'icon_url' => $vs_icon_url = $o_media_coder->getMediaTag($va_movement_type_info[$vn_type_id]['icon'], 'icon'),
						'typename_singular' => $vs_typename = $va_movement_type_info[$vn_type_id]['name_singular'],
						'typename_plural' => $va_movement_type_info[$vn_type_id]['name_plural'],
						'icon' => '<div class="caUseHistoryIconContainer" style="background-color: #'.$vs_color.'"><div class="caUseHistoryIcon">'.($vs_icon_url ? $vs_icon_url : '<div class="caUseHistoryIconText">'.$vs_typename.'</div>').'</div></div>',
						'date' => $va_date['display']
					);
				}
			}
		}
		
		
		// Occurrences
		$va_occurrences = $this->get('ca_occurrences.occurrence_id', array('returnAsArray' => true));
		if(is_array($va_occurrence_types = caGetOption('ca_occurrences_showTypes', $pa_bundle_settings, null)) && is_array($va_occurrences) && sizeof($va_occurrences)) {	
			$qr_occurrences = caMakeSearchResult('ca_occurrences', $va_occurrences);
			
			$t_occurrence = new ca_occurrences();
			$va_occurrence_type_info = $t_occurrence->getTypeList(); 
			
			$va_date_elements_by_type = array();
			foreach($va_occurrence_types as $vn_type_id) {
				if (!is_array($va_date_elements = caGetOption("ca_occurrences_{$va_occurrence_type_info[$vn_type_id]['idno']}_dateElement", $pa_bundle_settings, null)) && $va_date_elements) {
					$va_date_elements = array($va_date_elements);
				}
				if (!$va_date_elements) { continue; }
				$va_date_elements_by_type[$vn_type_id] = $va_date_elements;
			}
			
			while($qr_occurrences->nextHit()) {
				$vn_occurrence_id = $qr_occurrences->get('occurrence_id');
				$vn_type_id = $qr_occurrences->get('type_id');
				
				$va_dates = array();
				if (is_array($va_date_elements_by_type[$vn_type_id]) && sizeof($va_date_elements_by_type[$vn_type_id])) {
					foreach($va_date_elements_by_type[$vn_type_id] as $vs_date_element) {
						$va_dates[] = array(
							'sortable' => $qr_occurrences->get("ca_occurrences.{$vs_date_element}", array('getDirectDate' => true)),
							'display' => $qr_occurrences->get("ca_occurrences.{$vs_date_element}")
						);
					}
				}
				if (!sizeof($va_dates)) {
					$va_dates[] = array(
						'sortable' => $vn_date = caUnixTimestampToHistoricTimestamps($qr_occurrences->get('lastModified')),
						'display' => caGetLocalizedDate($vn_date)
					);
				}
		
				foreach($va_dates as $va_date) {
					if (!$va_date['sortable']) { continue; }
					if (!in_array($vn_type_id, $va_occurrence_types)) { continue; }
					
					$vs_color = $va_occurrence_type_info[$vn_type_id]['color'];
					if (!$vs_color || ($vs_color == '000000')) {
						$vs_color = caGetOption("ca_occurrences_{$va_occurrence_type_info[$vn_type_id]['idno']}_color", $pa_bundle_settings, 'ffffff');
					}
					
					$va_history[$va_date['sortable']][] = array(
						'type' => 'ca_occurrences',
						'id' => $vn_occurrence_id,
						'display' => $qr_occurrences->getWithTemplate(caGetOption("ca_occurrences_{$va_occurrence_type_info[$vn_type_id]['idno']}_displayTemplate", $pa_bundle_settings, '^ca_occurrences.preferred_labels.name (^ca_occurrences.idno)')),
						'color' => $vs_color,
						'icon_url' => $vs_icon_url = $o_media_coder->getMediaTag($va_occurrence_type_info[$vn_type_id]['icon'], 'icon'),
						'typename_singular' => $vs_typename = $va_occurrence_type_info[$vn_type_id]['name_singular'],
						'typename_plural' => $va_occurrence_type_info[$vn_type_id]['name_plural'],
						'icon' => '<div class="caUseHistoryIconContainer" style="background-color: #'.$vs_color.'"><div class="caUseHistoryIcon">'.($vs_icon_url ? $vs_icon_url : '<div class="caUseHistoryIconText">'.$vs_typename.'</div>').'</div></div>',
						'date' => $va_date['display']
					);
				}
			}
		}
		
		// Storage locations
		$va_locations = $this->get('ca_objects_x_storage_locations.relation_id', array('returnAsArray' => true));
	
		if(is_array($va_location_types = caGetOption('ca_storage_locations_showRelationshipTypes', $pa_bundle_settings, null)) && is_array($va_locations) && sizeof($va_locations)) {	
			$t_location = new ca_storage_locations();
			$va_location_type_info = $t_location->getTypeList(); 
			
			$vs_name_singular = $t_location->getProperty('NAME_SINGULAR');
			$vs_name_plural = $t_location->getProperty('NAME_PLURAL');
			
			$qr_locations = caMakeSearchResult('ca_objects_x_storage_locations', $va_locations);
			
			while($qr_locations->nextHit()) {
				$vn_location_id = $qr_locations->get('ca_objects_x_storage_locations.location_id');
				
				$va_date = array(
					'sortable' => $qr_locations->get("ca_objects_x_storage_locations.effective_date", array('getDirectDate' => true)),
					'display' => $qr_locations->get("ca_objects_x_storage_locations.effective_date")
				);

				if (!$va_date['sortable']) { continue; }
				if (!in_array($vn_rel_type_id = $qr_locations->get('ca_objects_x_storage_locations.type_id'), $va_location_types)) { continue; }
				$vn_type_id = $qr_locations->get('ca_storage_locations.type_id');
				
				$vs_color = $va_location_type_info[$vn_type_id]['color'];
				if (!$vs_color || ($vs_color == '000000')) {
					$vs_color = caGetOption("ca_storage_locations_color", $pa_bundle_settings, 'ffffff');
				}
				
				$va_history[$va_date['sortable']][] = array(
					'type' => 'ca_storage_locations',
					'id' => $vn_location_id,
					'display' => $qr_locations->getWithTemplate("<unit relativeTo='ca_storage_locations'>".caGetOption('ca_storage_locations_displayTemplate', $pa_bundle_settings, '^ca_storage_locations.idno')."</unit>"),
					'color' => $vs_color,
					'icon_url' => $vs_icon_url = $o_media_coder->getMediaTag($va_location_type_info[$vn_type_id]['icon'], 'icon'),
					'typename_singular' => $vs_name_singular, //$vs_typename = $va_location_type_info[$vn_type_id]['name_singular'],
					'typename_plural' => $vs_name_plural, //$va_location_type_info[$vn_type_id]['name_plural'],
					'icon' => '<div class="caUseHistoryIconContainer" style="background-color: #'.$vs_color.'"><div class="caUseHistoryIcon">'.($vs_icon_url ? $vs_icon_url : '<div class="caUseHistoryIconText">'.$vs_name_singular.'</div>').'</div></div>',
					'date' => $va_date['display']
				);
			}
		}
	//	print_R($va_history);
		
		ksort($va_history);
		$va_history = array_reverse($va_history);
		$o_view->setVar('history', $va_history);
		
		return $o_view->render('ca_objects_history.php');
 	}
 	# ------------------------------------------------------
 	/**
 	 *
 	 */
 	public function getLastMovement($pa_options=null) {
 		$pn_object = caGetOption('object_id', $pa_options, null);
 		if (!($vn_object_id = ($pn_object_id > 0) ? $pn_object_id : $this->getPrimaryKey())) { return null; }
 		
 		if (!($ps_date_element = caGetOption('dateElement', $pa_options, null))) { return null; }
 		if (!($t_element = $this->_getElementInstance($ps_date_element))) { return null; }
 		
 		$va_current_date = caDateToHistoricTimestamps(_t('now'));
 		$vn_current_date = $va_current_date['start'];
 		
 		$o_db = $this->getDb();
 		$qr_res = $o_db->query("
 			SELECT cmo.relation_id
 			FROM ca_movements_x_objects cmo
 			INNER JOIN ca_movements AS m ON m.movement_id = cmo.movement_id
 			INNER JOIN ca_attributes AS a ON a.row_id = m.movement_id
 			INNER JOIN ca_attribute_values AS av ON av.attribute_id = a.attribute_id
 			WHERE
 				(cmo.object_id = ?) AND 
 				(av.element_id = ?) AND (a.table_num = 137) AND 
 				(m.deleted = 0) AND (av.value_decimal1 <= ?)
 			ORDER BY
 				av.value_decimal1 DESC, cmo.relation_id DESC
 			LIMIT 1
 		", array($vn_object_id, (int)$t_element->getPrimaryKey(), $vn_current_date));
 		if($qr_res->nextRow()) {
 			return new ca_movements_x_objects($qr_res->get('relation_id'));
 		}
 		return false;
 	}
 	# ------------------------------------------------------
 	/**
 	 *
 	 *
 	 * @param array $pa_options Array of options:
 	 *		template =
 	 *		dateElement = 
 	 */
 	public function getMovementHistory($pa_options=null) {
 		$pn_object = caGetOption('object_id', $pa_options, null);
 		if (!($vn_object_id = ($pn_object_id > 0) ? $pn_object_id : $this->getPrimaryKey())) { return null; }
 		
 		$ps_display_template = caGetOption('template', $pa_options, '^ca_movements_x_objects.relation_id');
 		
 		if (!($ps_date_element = caGetOption('dateElement', $pa_options, null))) { return null; }
 		if (!($t_element = $this->_getElementInstance($ps_date_element))) { return null; }
 		
 		$va_current_date = caDateToHistoricTimestamps(_t('now'));
 		$vn_current_date = $va_current_date['start'];
 		
 		
 		//
 		// Directly query the date attribute for performance
 		// 
 		$o_dm = Datamodel::load();
 		$vn_movements_table_num = (int)$o_dm->getTableNum("ca_movements");
 		
 		$o_db = $this->getDb();
 		$qr_res = $o_db->query("
 			SELECT cmo.relation_id, cmo.movement_id, cmo.object_id, av.value_decimal1
 			FROM ca_movements_x_objects cmo
 			INNER JOIN ca_movements AS m ON m.movement_id = cmo.movement_id
 			INNER JOIN ca_attributes AS a ON a.row_id = m.movement_id
 			INNER JOIN ca_attribute_values AS av ON av.attribute_id = a.attribute_id
 			WHERE
 				(cmo.object_id = ?) AND 
 				(av.element_id = ?) AND (a.table_num = ?) AND 
 				(m.deleted = 0)
 			ORDER BY
 				av.value_decimal1 DESC, cmo.relation_id DESC
 		", array($vn_object_id, (int)$t_element->getPrimaryKey(), $vn_movements_table_num));
 		
 		
 		$va_relation_ids = $qr_res->getAllFieldValues('relation_id');
 		$va_displays = caProcessTemplateForIDs($ps_display_template, 'ca_movements_x_objects', $va_relation_ids, array('returnAsArray' => true));
 
		$qr_res->seek(0);
 		$va_items = array();
 		
 		$vb_have_seen_the_present = false;
 		while($qr_res->nextRow()) {
 			$va_row = $qr_res->getRow();
 			$vn_relation_id = $va_row['relation_id'];
 			
 			if ($va_row['value_decimal1'] > $vn_current_date) { 
 				$vs_status = 'FUTURE';
 			} else {
 				$vs_status = $vb_have_seen_the_present ? 'PAST' : 'PRESENT';
 				$vb_have_seen_the_present = true;
 			}
 			
 			$va_items[$vn_relation_id] = array(
 				'object_id' => $va_row['object_id'],
 				'movement_id' => $va_row['movement_id'],
 				'display' => array_shift($va_displays),
 				'status' => $vs_status
 			);
 		}
 		return $va_items;
 	}
 	# ------------------------------------------------------
 	/**
 	 *
 	 */
 	public function getLastLocation($pa_options=null) {
 		$pn_object = caGetOption('object_id', $pa_options, null);
 		if (!($vn_object_id = ($pn_object_id > 0) ? $pn_object_id : $this->getPrimaryKey())) { return null; }
 		
 		$va_current_date = caDateToHistoricTimestamps(_t('now'));
 		$vn_current_date = $va_current_date['start'];
 		
 		$o_db = $this->getDb();
 		$qr_res = $o_db->query("
 			SELECT csl.relation_id
 			FROM ca_objects_x_storage_locations csl
 			INNER JOIN ca_storage_locations AS sl ON sl.location_id = csl.location_id
 			WHERE
 				(csl.object_id = ?) AND 
 				(sl.deleted = 0) AND (csl.sdatetime <= ?)
 			ORDER BY
 				csl.sdatetime DESC, csl.relation_id DESC
 			LIMIT 1
 		", array($vn_object_id, $vn_current_date));
 	
 		if($qr_res->nextRow()) {
 			return new ca_objects_x_storage_locations($qr_res->get('relation_id'));
 		}
 		return false;
 	}
 	# ------------------------------------------------------
 	/**
 	 *
 	 *
 	 * @param array $pa_options Array of options:
 	 *		template =
 	 */
 	public function getLocationHistory($pa_options=null) {
 		$pn_object = caGetOption('object_id', $pa_options, null);
 		if (!($vn_object_id = ($pn_object_id > 0) ? $pn_object_id : $this->getPrimaryKey())) { return null; }
 		
 		$ps_display_template = caGetOption('template', $pa_options, '^ca_objects_x_storage_locations.relation_id');
 		
 		$va_current_date = caDateToHistoricTimestamps(_t('now'));
 		$vn_current_date = $va_current_date['start'];
 		
 		
 		//
 		// Directly query the date field for performance
 		// 
 		
 		$o_db = $this->getDb();
 		$qr_res = $o_db->query("
 			SELECT csl.relation_id, csl.location_id, csl.object_id, csl.sdatetime, csl.edatetime
 			FROM ca_objects_x_storage_locations csl
 			INNER JOIN ca_storage_locations AS sl ON sl.location_id = csl.location_id
 			WHERE
 				(csl.object_id = ?) AND 
 				(sl.deleted = 0)
 			ORDER BY
 				 csl.sdatetime DESC, csl.relation_id DESC
 		", array($vn_object_id));
 		
 		
 		$va_relation_ids = $qr_res->getAllFieldValues('relation_id');
 		$va_displays = caProcessTemplateForIDs($ps_display_template, 'ca_objects_x_storage_locations', $va_relation_ids, array('returnAsArray' => true));
 
		$qr_res->seek(0);
 		$va_items = array();
 		
 		$vb_have_seen_the_present = false;
 		while($qr_res->nextRow()) {
 			$va_row = $qr_res->getRow();
 			$vn_relation_id = $va_row['relation_id'];
 			
 			if ($va_row['sdatetime'] > $vn_current_date) { 
 				$vs_status = 'FUTURE';
 			} else {
 				$vs_status = $vb_have_seen_the_present ? 'PAST' : 'PRESENT';
 				$vb_have_seen_the_present = true;
 			}
 			
 			$va_items[$vn_relation_id] = array(
 				'object_id' => $va_row['object_id'],
 				'location_id' => $va_row['location_id'],
 				'display' => array_shift($va_displays),
 				'status' => $vs_status
 			);
 		}
 		return $va_items;
 	}
 	# ------------------------------------------------------
 	# Components
 	# ------------------------------------------------------
	/** 
	 * Returns HTML form bundle for component listing
	 *
	 * @param HTTPRequest $po_request The current request
	 * @param string $ps_form_name
	 * @param string $ps_placement_code
	 * @param array $pa_bundle_settings
	 * @param array $pa_options Array of options. Options include:
	 *			noCache = If set to true then label cache is bypassed. [Default = true]
	 *
	 * @return string Rendered HTML bundle
	 */
	public function getComponentListHTMLFormBundle($po_request, $ps_form_name, $ps_placement_code, $pa_bundle_settings=null, $pa_options=null) {
		global $g_ui_locale;
		
		$o_view = new View($po_request, $po_request->getViewsDirectoryPath().'/bundles/');
		
		if(!is_array($pa_options)) { $pa_options = array(); }
		
		$o_view->setVar('id_prefix', $ps_form_name);
		$o_view->setVar('placement_code', $ps_placement_code);		// pass placement code
		
		$o_view->setVar('settings', $pa_bundle_settings);
		
		$o_view->setVar('add_label', isset($pa_bundle_settings['add_label'][$g_ui_locale]) ? $pa_bundle_settings['add_label'][$g_ui_locale] : null);
		$o_view->setVar('t_subject', $this);
		
		
		
		return $o_view->render('ca_objects_components_list.php');
	}
	# ------------------------------------------------------
	/** 
	 * Return number of components directly linked to this container
	 *
	 * @param array $pa_options Array of options:
	 *			object_id = object to return components for, rather than currently loaded object. [Default = null; use loaded object]
	 *	
	 * @return int 
	 */
	public function getComponentCount($pa_options=null) {
		$pn_object_id = caGetOption('object_id', $pa_options, null);
		if (!(int)$pn_object_id) { $pn_object_id = (int)$this->getPrimaryKey(); }
		if (!$pn_object_id) { return null; }
		
		$va_component_types = $this->getAppConfig()->getList('ca_objects_component_types');
		if (!is_array($va_component_types) || !sizeof($va_component_types)) { return 0; }
		
		$va_ids = ca_objects::find(array('parent_id' => $pn_object_id, 'type_id' => $va_component_types, array('returnAs' => 'ids')));
	
		if (!is_array($va_ids)) { return 0; }
		return sizeof($va_ids);	
	}
	# ------------------------------------------------------
	/** 
	 * Return number of components directly linked to this container
	 *
	 * @param array $pa_options Array of options:
	 *		object_id = object to return components for, rather than currently loaded object. [Default = null; use loaded object]
	 *		returnAs = what to return; possible values are:
	 *			searchResult			= a search result instance (aka. a subclass of BaseSearchResult), when the calling subclass is searchable (ie. <classname>Search and <classname>SearchResult classes are defined) 
	 *			ids						= an array of ids (aka. primary keys)
	 *			modelInstances			= an array of instances, one for each match. Each instance is the same class as the caller, a subclass of BaseModel 
	 *			firstId					= the id (primary key) of the first match. This is the same as the first item in the array returned by 'ids'
	 *			firstModelInstance		= the instance of the first match. This is the same as the first instance in the array returned by 'modelInstances'
	 *			info					= an array, keyed on object_id with label, type and idno of each component
	 *			
	 * @return int 
	 */
	public function getComponents($pa_options=null) {
		$pn_object_id = caGetOption('object_id', $pa_options, null);
		if (!(int)$pn_object_id) { $pn_object_id = (int)$this->getPrimaryKey(); }
		if (!$pn_object_id) { return null; }
		
		if (caGetOption('idsOnly', $pa_options, false)) { $pa_options['returnAs'] = 'ids'; }
		$vs_return_as = caGetOption('returnAs', $pa_options, 'info');
	
		$va_component_types = $this->getAppConfig()->getList('ca_objects_component_types');
		if (!is_array($va_component_types) || !sizeof($va_component_types)) { return 0; }
		
		$vm_res = ca_objects::find(array('parent_id' => $pn_object_id, 'type_id' => $va_component_types), array('sort' => 'ca_objects.idno', 'returnAs' => ($vs_return_as == 'info') ? 'searchResult' : $vs_return_as));
	
		if ($vs_return_as == 'info') {
			$va_data = array();
			while($vm_res->nextHit()) {
				$va_data[$vn_object_id = $vm_res->get('ca_objects.object_id')] = array(
					'object_id' => $vn_object_id,
					'id' => $vn_object_id,
					'label' => $vm_res->get('ca_objects.preferred_labels.name'),
					'idno' => $vm_res->get('ca_objects.idno'),
					'type_id' => $vm_res->get('ca_objects.type_id'),
					'source_id' => $vm_res->get('ca_objects.source_id')
				);
			}
			
			return caSortArrayByKeyInValue($va_data, array('idno'));
		}
		return $vm_res;	
	}
	# ------------------------------------------------------
	/** 
	 * Check if currently loaded object is a component container
	 *	
	 * @return bool 
	 */
	public function canTakeComponents() {
		$va_component_types = $this->getAppConfig()->getList('ca_objects_container_types');
		if (!is_array($va_component_types) || !sizeof($va_component_types)) { return false; }
		return in_array($this->getTypeCode(), $va_component_types);
	}
 	# ------------------------------------------------------
}
?>