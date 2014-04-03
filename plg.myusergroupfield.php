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
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		global $_CB_framework, $ueConfig, $_CB_database;

		$oReturn = null;
		
/*		$fieldtype = $field->params->get( 'fieldType', 'single' );*/
/*		$fieldsubstitute = $field->params->get( 'fieldSubstitute', 'no' );*/
		$fieldwhere = $field->params->get( 'fieldWhere', '' );
		$fieldgroupid = $field->params->get( 'fieldGroupe','0');
		$viewer =& JFactory::getUser();
		
	
		    // Built MySQL Query
		    $query = "UPDATE #__user_usergroup_map SET group_id = (SELECT id FROM #__usergroups WHERE title=(SELECT " . $field->params->get( 'fieldSelect', '' ) . " AS mysqlfield";
		    $query .= " FROM " . $field->params->get( 'fieldFrom', '' );
		    
		    if($fieldwhere != '') {
               $query .= " WHERE " . $fieldwhere.")) WHERE user_id =". $user->id." AND group_id=".$fieldgroupid;
		    }
/*			$fieldorder = $field->params->get( 'fieldOrder', '' );*/
/*		    if($fieldorder != '' && $fieldtype == 'multi') {
               $query .= " ORDER BY " . $fieldorder;
		    }
			$fieldlimit = $field->params->get( 'fieldLimit', '' );
		    if($fieldlimit != '' && $fieldtype == 'multi') {
               $query .= " LIMIT " . $fieldlimit;
		    }*/
		    
		    $_CB_database->setQuery(str_replace("{USERID}", $user->id, str_replace("{VIEWERID}", $viewer->id, $query)));
			$_CB_database->query();
          
/*		    $fieldprefix = $field->params->get( 'fieldPrefix', '' );
		    $fielddelimiter = $field->params->get( 'fieldDelimiter', '' );
            $fieldsuffix = $field->params->get( 'fieldSuffix', '' );*/
            
           /* if($fieldtype == 'single') {
            	$value = $_CB_database->loadResult();
            } else {
            	$results = $_CB_database->loadObjectList();
            	if(count($results) > 0) {
            		$first = true;            	
	            	foreach ($results as $result) {
	            		if ($first == true) {
	            			$value = $result->mysqlfield;
	            			$first = false;
	            		} else {
	            			$value .= $fielddelimiter . $result->mysqlfield;
	            		}
	            	}
            	} else {
            		$value = null;
            	}	
            }*/
            
/*            if ( $value != '' ) {
                $value = $fieldprefix . $value . $fieldsuffix;
                $fieldmode = $field->params->get( 'fieldMode', 'display' );
                $fieldsavename = $field->params->get( 'fieldSaveName', '' );
                
                if ( $fieldmode != 'display' ) {
                //	$_CB_database->setQuery( "UPDATE #__user_usergroup_map SET group_id = (SELECT id FROM #__usergroups WHERE title='" . $value . "') WHERE user_id = " . $user->id );
                //	$_CB_database->query();
                	if ( $fieldmode == 'save' ) {
                		$value = null;
                	}
                }
            } else {
            	$value = null;
            }*/
		
		
		/*if ( $value && $fieldsubstitute != "no" ) {
			if ( $fieldsubstitute == "viewer" ) {
				$usersub =& CBUser::getInstance( (int)$viewer->id );
			} else {
				$usersub =& CBUser::getInstance( (int)$user->id );
			}
			$value = $usersub->replaceUserVars( $value );
		}*/

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
	}
}//end of My User Group Field field
?>
