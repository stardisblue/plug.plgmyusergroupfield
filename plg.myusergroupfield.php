<?php
/**
* Joomla Community Builder User Groupe Field Type Plugin: plug_plgmyusergroupfield
* @version $Id$
* @package plug_plgmyusergroupfield
* @subpackage plg.myusergroupfield.php
* @author CHEN Fati
* @copyright (C) 2014
* @license Limited http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
* @final 1.0.0
*/

/** ensure this file is being included by a parent file */
if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;
$_PLUGINS->loadPluginGroup( 'user', array( (int) 1 ) );
$_PLUGINS->registerUserFieldTypes( array( 'myusergroupfield' => 'CBfield_myusergroup' ) );
$_PLUGINS->registerUserFieldParams();

class CBfield_myusergroup extends CBfield_select_multi_radio {
	/**
	 * Accessor :
	 * Returns a field in specified format
	 *
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason      'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed     
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		global $_CB_framework, $ueConfig, $_CB_database;

		$ret = null;
		
		$fieldtype = (int) $field->params->get('fieldType', '0' );
		
		$_CB_database->setQuery( "SELECT title FROM #__usergroup"
		. "\n WHERE parentid = 12"
		. "\n ORDER BY title ASC" );
		$Values = $_CB_database->loadObjectList('title');

//		if (! is_array( $Values ) ) {
//			$Values = array();
//		}
//		foreach ( $Values as $k => $v ) {
//			if ( ! in_array( cbGetUnEscaped( $v ), $Values ) ) {
//				unset( $value[$k] );
//			}
//		} 
//		$value=implode("|*|",$Values);
		$value="Etudiant|*|Enseignant|*|AncienEtudiant"
		
		switch ( $output ) {
			case 'htmledit':
				if ( $reason == 'search' ) {
					$ret=	$this->_fieldSearchModeHtml( $field, $user, $this->_fieldEditToHtml( $field, $user, $reason, 'input', 'select', $value, '' ), 'text', $list_compare_types );
				} else {
					$ret=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'select', $value, '' );
				}
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

}//end of My User Group field
?>
