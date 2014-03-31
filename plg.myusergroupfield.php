<?php
/**
* Joomla Community Builder User Groupe Field Type Plugin: plug_plgmyusergroupfield
* @version $Id$
* @package plug_plgmyusergroupfield
* @subpackage plg.myusergroupfield.php
* @author CHEN Fati
* @copyright (C) 2014
* @license Limited http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
* @final 1.0
*/

/** ensure this file is being included by a parent file */
if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;
$_PLUGINS->loadPluginGroup( 'user', array( (int) 1 ) );
$_PLUGINS->registerUserFieldTypes( array( 'myusergroupfield' => 'CBfield_myusergroup' ) );
$_PLUGINS->registerUserFieldParams();

class CBfield_myusergroup extends CBfield_select_multi_radio {
	/**
	 * Acessor :
	 * Returns a field in specified format
	 *
	 * @param  int                   Parent id.
	 * @return mixed     
	 *
	 *
	 *
	 *         
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		global $_CB_framework, $ueConfig, $_CB_database;

		$ret = null;
		
		$fieldtype = $field->params->get('fieldType', '' );
		
		if ($value === null) {
			$value = array();
		}
		$_CB_database->setQuery( "SELECT title FROM #__usergroup"
		. "\n WHERE parentid = " . (int) $fieldtype
		. "\n ORDER BY ordering" );
		$Values = $_CB_database->loadResultArray();
		if (! is_array( $Values ) ) {
			$Values = array();
		}
		foreach ( $value as $k => $v ) {
			if ( ! in_array( cbGetUnEscaped( $v ), $Values ) ) {
				unset( $value[$k] );
			}
		}



		switch ( $output ) {
			case 'htmledit':
					$ret			=	$this->_fieldEditToHtml( $field, $user, $reason, 'select', 'text', $value, '' );
				break;
			case 'html':
			case 'rss':
			case 'json':
			case 'php':
			case 'xml':
			case 'csvheader':
			case 'fieldslist':
			case 'csv':
			default:
				$ret				=	parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}
		return $ret;
	}

	/**
	 * Mutator:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		foreach ( $field->getTableColumns() as $col ) {
			$value					=	cbGetParam( $postdata, $col );
			if ( ! is_array( $value ) ) {
				$value				=	stripslashes( $value );
				$validated			=	$this->validate( $field, $user, $col, $value, $postdata, $reason );
				if ( $value === '' ) {
					$value			=	null;
				}

				if ( $validated && isset( $user->$col ) && ( ( (string) $user->$col ) !== (string) $value ) ) {
					$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );
				}
				$user->$col		=	$value;
			}
		}
	}


	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $searchVals  RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string                $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return array of cbSqlQueryPart
	 */

	function _intToSql( &$field, $col, $value, $operator, $searchMode ) {
		$value							=	(int) $value;
		// $this->validate( $field, $user, $col, $value, $postdata, $reason );
		$sql							=	new cbSqlQueryPart();
		$sql->tag						=	'column';
		$sql->name						=	$col;
		$sql->table						=	$field->table;
		$sql->type						=	'sql:field';
		$sql->operator					=	$operator;
		$sql->value						=	$value;
		$sql->valuetype					=	'const:int';
		$sql->searchmode				=	$searchMode;
		return $sql;
	}

}//end of My User Group field
?>