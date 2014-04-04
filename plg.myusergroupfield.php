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
$_PLUGINS->registerUserFieldTypes( array( 'myusergroupfield' => 'CBfield_myusergroupfield' ) );
$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerFunction( 'onAfterUserRegistration', 'changeUser','CBfield_myusergroupfield' );

class CBfield_myusergroupfield extends CBfield_counter {
	/**
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function changeUser(&$user){
		global $_CB_framework, $ueConfig, $_CB_database;
		
		$oReturn = null;
		$query = "SELECT params FROM #__comprofiler_fields WHERE type='mysqlfield'";
		$_CB_database->setQuery($query);
		$value = $_CB_database->loadResult();
		
		$params=preg_split('/field[^=]*=/i', $value, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		$viewer =& JFactory::getUser();
		
		// Built MySQL Query
		$query = "UPDATE #__user_usergroup_map SET group_id = (SELECT id FROM #__usergroups WHERE title=(SELECT " . $params[0] . " AS mysqlfield";
		$query .= " FROM " . $params[1];
		$query .= " WHERE " . $params[2].")) WHERE user_id =". $user->id." AND group_id=".$params[3];

		$_CB_database->setQuery(str_replace("{USERID}",$user->id, str_replace("{VIEWERID}", $viewer->id, $query)));
		$_CB_database->query();
		return $oReturn;
	}
	
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		global $_CB_framework, $ueConfig, $_CB_database;

		$oReturn = null;
		$fieldwhere = $field->params->get( 'fieldWhere', '' );
		$fieldgroupid = $field->params->get( 'fieldGroupe','0');
		$viewer =& JFactory::getUser();


		// Built MySQL Query
		$query = "UPDATE #__user_usergroup_map SET group_id = (SELECT id FROM #__usergroups WHERE title=(SELECT " . $field->params->get( 'fieldSelect', '' ) . " AS mysqlfield";
		$query .= " FROM " . $field->params->get( 'fieldFrom', '' );

		if($fieldwhere != '') {
			$query .= " WHERE " . $fieldwhere.")) WHERE user_id =". $user->id." AND group_id=".$fieldgroupid;
		}

		$_CB_database->setQuery(str_replace("{USERID}", $user->id, str_replace("{VIEWERID}", $viewer->id, $query)));
		$_CB_database->query();

		switch ( $output ) {
			case 'html':
			case 'rss':
				$oReturn				=	null;
				break;

			case 'htmledit':
				// $oReturn				=	parent::getField( $field, $user, $output, $reason, $list_compare_types );
				$oReturn				=	null;		//TBD for now no searches...not optimal in SQL anyway.
				break;

			case 'json':
			case 'php':
			case 'xml':
			case 'csvheader':
			case 'fieldslist':
			case 'csv':
			default:
				$oReturn				=	$this->_formatFieldOutputIntBoolFloat( $field->name, $value, $output );
				break;
		}
		return $oReturn;
}//end of My User Group Field field
?>
